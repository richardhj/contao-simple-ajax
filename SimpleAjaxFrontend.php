<?php

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2011 Leo Feyer
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 * @copyright      Leo Unglaub 2012
 * @author         Leo Unglaub <leo@leo-unglaub.net>
 * @package        simple_ajax
 * @license        LGPL
 */


// simple trick for Contao < 2.10
$arrPost = $_POST;
unset($_POST);


// inizialize the contao framework
define('TL_MODE', 'FE');
require('system/initialize.php');


// write the post data back into the array
$_POST = $arrPost;


/**
 * Class SimpleAjaxFrontend
 * Contain methods to catch ajax requests and send responses back to the client.
 *
 * Description
 * ```````````
 *
 * The usage of the SimpleAjax extensions is very easy. The extension provides
 * the hook $GLOBALS['TL_HOOKS']['simpleAjax'] for extension developers. You
 * simply have to register your class/method and the extension will call
 * your class if there is an incomming ajax request.
 *
 * You simply have to deside if it's an ajax request for your module and return
 * the data you want.
 *
 * There are a few thinks you should know about the extension:
 *    * YOU have the full controll over the response. That also means
 *      that you have to set the correct header.
 *  * YOU have to terminate the request after you have send the response.
 *    * If the ajax request is not for your method you simply have nothing to
 *    * return.
 *
 *
 * Usage
 * `````
 * // config.php
 * $GLOBALS['TL_HOOKS']['simpleAjaxFrontend'][] = array('MyClassFrontend', 'myMethod');
 *
 * // MyClassFrontend.php
 * class MyClassFrontend extends System
 * {
 *    public function myMethod()
 *    {
 *        if ($this->Input->get('acid') == 'myrequest')
 *        {
 *            $arrReturn = array('foo', 'bar', 'foobar');
 *
 *            header('Content-Type: application/json');
 *            echo json_encode($arrReturn);
 *            exit;
 *        }
 *    }
 * }
 */
class SimpleAjaxFrontend extends Frontend
{

    /**
     * Set some important constants
     *
     * @param    void
     *
     * @return    void
     */
    public function __construct()
    {
        $this->import('FrontendUser');

        // call the constructor from Frontend
        parent::__construct();

        // check if a user is logged in
        define('BE_USER_LOGGED_IN', $this->getLoginStatus('BE_USER_AUTH'));
        define('FE_USER_LOGGED_IN', $this->getLoginStatus('FE_USER_AUTH'));

        // set static url's in case the user generated HTML code
        \Controller::setStaticUrls();
    }


    /**
     * Get the ajax request and call all hooks
     *
     * @param    void
     *
     * @return    void
     */
    public function run()
    {
        // run the global hook
        if (is_array($GLOBALS['TL_HOOKS']['simpleAjax']) && count($GLOBALS['TL_HOOKS']['simpleAjax']) > 0) {
            // execute every registered callback
            foreach ($GLOBALS['TL_HOOKS']['simpleAjax'] as $callback) {
                $this->import($callback[0]);
                $this->{$callback[0]}->{$callback[1]}();
            }
        }

        // run the frontend exclusive hook
        if (is_array($GLOBALS['TL_HOOKS']['simpleAjaxFrontend']) && count(
                $GLOBALS['TL_HOOKS']['simpleAjaxFrontend']
            ) > 0
        ) {
            // execute every registered callback
            foreach ($GLOBALS['TL_HOOKS']['simpleAjaxFrontend'] as $callback) {
                $this->import($callback[0]);
                $this->{$callback[0]}->{$callback[1]}();
            }
        }

        // if there is no other output, we generate a 412 error response
        header('HTTP/1.1 412 Precondition Failed');
        die('Simple Ajax: Invalid AJAX call.');
    }
}


// create a SimpleAjax instance and run it
$objSimpleAjaxFrontend = new SimpleAjaxFrontend();
$objSimpleAjaxFrontend->run();

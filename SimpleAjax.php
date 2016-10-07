<?php
/**
 * SimpleAjax extension for Contao Open Source CMS
 *
 * Copyright (c) 2012 Leo Unglaub, 2016 Richard Henkenjohann
 *
 * @package SimpleAjax
 * @author  Leo Unglaub <leo@leo-unglaub.net>
 * @author  Richard Henkenjohann <richardhenkenjohann@googlemail.com>
 */


// Initialize the Contao framework
define('TL_MODE', 'FE');
/** @noinspection PhpIncludeInspection */
require('system/initialize.php');



/**
 * Class SimpleAjax
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
 * $GLOBALS['TL_HOOKS']['simpleAjax'][] = array('MyClass', 'myMethod');
 *
 * // MyClass.php
 * class MyClass extends System
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
class SimpleAjax extends Controller
{

    /**
     * Call the parent constructor.
     *
     * !!! DON'T REMOVE THIS !!!
     *
     * If you remove this you get the following error message:
     * Fatal error: Call to protected System::__construct() from invalid
     * context
     *
     * @param    void
     *
     * @return    void
     */
    public function __construct()
    {
        parent::__construct();
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
        // check if a hook is registered
        if (is_array($GLOBALS['TL_HOOKS']['simpleAjax']) && count($GLOBALS['TL_HOOKS']['simpleAjax']) > 0) {
            // execute every registered callback
            foreach ($GLOBALS['TL_HOOKS']['simpleAjax'] as $callback) {
                $this->import($callback[0]);
                $this->$callback[0]->$callback[1]();
            }
        }

        // if there is no other output, we generate a 412 error response
        header('HTTP/1.1 412 Precondition Failed');
        die('Simple Ajax: Invalid AJAX call.');
    }
}


// create a SimpleAjax instance and run it
$objSimpleAjax = new SimpleAjax();
$objSimpleAjax->run();

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
 * Class SimpleAjaxFrontend
 * Contains methods to catch ajax requests and send responses back to the client.
 */
class SimpleAjaxFrontend extends \Frontend
{

    /**
     * Set some important constants
     */
    public function __construct()
    {
        FrontendUser::getInstance();

        // Call the constructor from Frontend
        parent::__construct();

        // Check if a user is logged in
        define('BE_USER_LOGGED_IN', $this->getLoginStatus('BE_USER_AUTH'));
        define('FE_USER_LOGGED_IN', $this->getLoginStatus('FE_USER_AUTH'));

        // Set static url's in case the user generated HTML code
        \Controller::setStaticUrls();
    }


    /**
     * Get the ajax request and call all hooks
     */
    public function run()
    {
        // Run the global hooks
        if (is_array($GLOBALS['TL_HOOKS']['simpleAjax']) && count($GLOBALS['TL_HOOKS']['simpleAjax']) > 0) {
            // Execute every registered callback
            foreach ($GLOBALS['TL_HOOKS']['simpleAjax'] as $callback) {
                if (is_array($callback)) {
                    System::importStatic($callback[0])->{$callback[1]}();
                } elseif (is_callable($callback)) {
                    $callback();
                }
            }
        }

        // Run the frontend exclusive hooks
        if (is_array($GLOBALS['TL_HOOKS']['simpleAjaxFrontend'])
            && count($GLOBALS['TL_HOOKS']['simpleAjaxFrontend']) > 0
        ) {
            // Execute every registered callback
            foreach ($GLOBALS['TL_HOOKS']['simpleAjaxFrontend'] as $callback) {
                if (is_array($callback)) {
                    System::importStatic($callback[0])->{$callback[1]}();
                } elseif (is_callable($callback)) {
                    $callback();
                }
            }
        }

        // If there is no other output, we generate a 412 error response
        header('HTTP/1.1 412 Precondition Failed');
        die('Simple Ajax: Invalid AJAX call.');
    }
}

// create a SimpleAjax instance and run it
$simpleAjaxFrontend = new SimpleAjaxFrontend();
$simpleAjaxFrontend->run();

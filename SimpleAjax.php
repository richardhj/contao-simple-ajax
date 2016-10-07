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
 * Contains methods to catch ajax requests and send responses back to the client.
 */
class SimpleAjax extends \Controller
{

    /**
     * Call the parent constructor
     */
    public function __construct()
    {
        parent::__construct();
    }


    /**
     * Get the ajax request and call all hooks
     */
    public function run()
    {
        // Check if a hook is registered
        if (is_array($GLOBALS['TL_HOOKS']['simpleAjax']) && count($GLOBALS['TL_HOOKS']['simpleAjax']) > 0) {
            // Execute every registered callback
            foreach ($GLOBALS['TL_HOOKS']['simpleAjax'] as $callback) {
                if (is_array($callback)) {
                    System::importStatic($callback[0])->{$callback[1]};
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
$simpleAjax = new SimpleAjax();
$simpleAjax->run();

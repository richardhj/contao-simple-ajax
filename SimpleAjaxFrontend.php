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

// Run the controller
$controller = new SimpleAjax\SimpleAjax();
$controller->setIncludeFrontendExclusive(true);
$controller->handle();

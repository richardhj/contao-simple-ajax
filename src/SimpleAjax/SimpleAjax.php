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

namespace SimpleAjax;


use Symfony\Component\EventDispatcher\EventDispatcher;
use SimpleAjax\Event\SimpleAjax as SimpleAjaxEvent;


/**
 * Class SimpleAjax
 * @package SimpleAjax
 */
class SimpleAjax extends \Frontend
{

    /**
     * Indicates whether the "SimpleAjaxFrontend.php" was called
     *
     * @var bool
     */
    protected $includeFrontendExclusive = false;


    /**
     * @param bool $includeFrontendExclusive
     *
     * @return SimpleAjax
     */
    public function setIncludeFrontendExclusive($includeFrontendExclusive)
    {
        $this->includeFrontendExclusive = $includeFrontendExclusive;

        return $this;
    }


    /**
     * @return bool
     */
    public function isIncludeFrontendExclusive()
    {
        return $this->includeFrontendExclusive;
    }


    /**
     * Initialize the object
     */
    public function __construct()
    {
        // Load the user object before calling the parent constructor
        \FrontendUser::getInstance();
        parent::__construct();

        // Check whether a user is logged in
        define('BE_USER_LOGGED_IN', $this->getLoginStatus('BE_USER_AUTH'));
        define('FE_USER_LOGGED_IN', $this->getLoginStatus('FE_USER_AUTH'));

        // No back end user logged in
        if (!$_SESSION['DISABLE_CACHE']) {
            // Maintenance mode (see #4561 and #6353)
            if (\Config::get('maintenanceMode')) {
                header('HTTP/1.1 503 Service Unavailable');
                die_nicely('be_unavailable', 'This site is currently down for maintenance. Please come back later.');
            }

            // Disable the debug mode
            \Config::set('debugMode', false);
        }

        // Set static url's in case the user generated HTML code
        \Controller::setStaticUrls();
    }


    /**
     * Handle the request
     */
    public function handle()
    {
        global $container;
        /** @var EventDispatcher $dispatcher */
        $dispatcher = $container['event-dispatcher'];

        // Trigger event
        $event = new SimpleAjaxEvent($this->isIncludeFrontendExclusive());
        $dispatcher->dispatch(SimpleAjaxEvent::NAME, $event);

        // Run hooks
        $this->runHooks();

        // If there is no other output, we generate a 412 error response
        header('HTTP/1.1 412 Precondition Failed');
        die('Simple Ajax: Invalid AJAX call.');
    }


    /**
     * Run hooks (to be removed)
     */
    private function runHooks()
    {
        // Run the global hooks
        if (is_array($GLOBALS['TL_HOOKS']['simpleAjax']) && count($GLOBALS['TL_HOOKS']['simpleAjax']) > 0) {
            // Execute every registered callback
            foreach ($GLOBALS['TL_HOOKS']['simpleAjax'] as $callback) {
                if (is_array($callback)) {
                    \System::importStatic($callback[0])->{$callback[1]}();
                } elseif (is_callable($callback)) {
                    $callback();
                }
            }
        }

        if ($this->isIncludeFrontendExclusive()) {
            // Run the frontend exclusive hooks
            if (is_array($GLOBALS['TL_HOOKS']['simpleAjaxFrontend'])
                && count($GLOBALS['TL_HOOKS']['simpleAjaxFrontend']) > 0
            ) {
                // Execute every registered callback
                foreach ($GLOBALS['TL_HOOKS']['simpleAjaxFrontend'] as $callback) {
                    if (is_array($callback)) {
                        \System::importStatic($callback[0])->{$callback[1]}();
                    } elseif (is_callable($callback)) {
                        $callback();
                    }
                }
            }
        }
    }
}

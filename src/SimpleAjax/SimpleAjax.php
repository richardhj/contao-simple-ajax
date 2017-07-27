<?php
/**
 * SimpleAjax extension for Contao Open Source CMS
 *
 * Copyright (c) 2012 Leo Unglaub, 2016-2017 Richard Henkenjohann
 *
 * @package SimpleAjax
 * @author  Leo Unglaub <leo@leo-unglaub.net>
 * @author  Richard Henkenjohann <richardhenkenjohann@googlemail.com>
 */

namespace SimpleAjax;


use Symfony\Component\EventDispatcher\EventDispatcher;
use SimpleAjax\Event\SimpleAjax as SimpleAjaxEvent;
use Symfony\Component\HttpFoundation\Response;


/**
 * Class SimpleAjax
 *
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

        $event = new SimpleAjaxEvent($this->isIncludeFrontendExclusive());
        $dispatcher->dispatch(SimpleAjaxEvent::NAME, $event);

        // If the event listener does not terminate the process by itself, check for a `Response to send (@since 1.2)
        $response = $event->getResponse();
        if (null === $response) {
            // Run hooks for backwards compatibility
            $this->runHooks();
            // Hooks should have been exiting now - if we end up, prepare an error response.
            $response = new Response(
                'Simple Ajax: Invalid AJAX call.',
                Response::HTTP_PRECONDITION_FAILED
            );
        }
        $response->send();
    }

    /**
     * Run hooks (legacy)
     */
    private function runHooks()
    {
        // Run the global hooks
        if (is_array($GLOBALS['TL_HOOKS']['simpleAjax'])
            && count($GLOBALS['TL_HOOKS']['simpleAjax']) > 0) {
            // Execute every registered callback
            foreach ($GLOBALS['TL_HOOKS']['simpleAjax'] as $callback) {
                if (is_array($callback)) {
                    \System::importStatic($callback[0])->{$callback[1]}();
                } elseif (is_callable($callback)) {
                    $callback();
                }
            }

            $this->triggerHooksDeprecatedNotice();
        }

        if ($this->isIncludeFrontendExclusive()) {
            // Run the frontend exclusive hooks
            if (is_array($GLOBALS['TL_HOOKS']['simpleAjaxFrontend'])
                && count($GLOBALS['TL_HOOKS']['simpleAjaxFrontend']) > 0) {
                // Execute every registered callback
                foreach ($GLOBALS['TL_HOOKS']['simpleAjaxFrontend'] as $callback) {
                    if (is_array($callback)) {
                        \System::importStatic($callback[0])->{$callback[1]}();
                    } elseif (is_callable($callback)) {
                        $callback();
                    }
                }

                $this->triggerHooksDeprecatedNotice();
            }
        }
    }

    /**
     * The Simple Ajax hooks are deprecated.
     *
     * If you encounter this deprecation notice, you might want to
     * 1. Remove the hook registration in the config.php and register your method in the event_listeners.php (or
     * applicable)
     * 2. Rewrite your code to set an `Symfony\Component\HttpFoundation\Response`. You do not have to terminate your
     * code with `exit()` anymore. This is the recommended way.
     */
    private function triggerHooksDeprecatedNotice()
    {
        @trigger_error(
            'The Simple Ajax hooks are deprecated, use the event to listen for.',
            E_USER_DEPRECATED
        );
    }
}

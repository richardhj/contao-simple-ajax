<?php
/**
 * SimpleAjax extension for Contao Open Source CMS
 *
 * Copyright (c) 2016-2018 Richard Henkenjohann
 *
 * @package SimpleAjax
 * @author  Richard Henkenjohann <richardhenkenjohann@googlemail.com>
 */

namespace SimpleAjax\Controller;

use Contao\System;
use SimpleAjax\Event\SimpleAjax as SimpleAjaxEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;

class SimpleAjaxController
{

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var bool
     */
    private $includeFrontendExclusive;

    /**
     * SimpleAjax constructor.
     *
     * @param EventDispatcherInterface $dispatcher The event dispatcher.
     */
    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @return bool
     */
    public function isIncludeFrontendExclusive()
    {
        return $this->includeFrontendExclusive;
    }

    /**
     * Handle a simple-ajax request.
     *
     * @param string $_frontend Run frontend-exclusive hooks.
     *
     * @return Response
     */
    public function __invoke($_frontend)
    {
        $this->includeFrontendExclusive = $_frontend === 'frontend';

        $event = new SimpleAjaxEvent($this->isIncludeFrontendExclusive());
        $this->dispatcher->dispatch(SimpleAjaxEvent::NAME, $event);

        // If the event listener does not terminate the process by itself, check for a `Response` to send (@since 1.2)
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

        return $response;
    }

    /**
     * Run hooks (legacy)
     *
     * @return void
     */
    private function runHooks()
    {
        // Run the global hooks
        if (is_array($GLOBALS['TL_HOOKS']['simpleAjax'])
            && count($GLOBALS['TL_HOOKS']['simpleAjax']) > 0) {
            $this->triggerHooksDeprecatedNotice();

            // Execute every registered callback
            foreach ($GLOBALS['TL_HOOKS']['simpleAjax'] as $callback) {
                if (is_array($callback)) {
                    System::importStatic($callback[0])->{$callback[1]}();
                } elseif (is_callable($callback)) {
                    $callback();
                }
            }
        }

        if ($this->isIncludeFrontendExclusive()) {
            // Run the frontend exclusive hooks
            if (is_array($GLOBALS['TL_HOOKS']['simpleAjaxFrontend'])
                && count($GLOBALS['TL_HOOKS']['simpleAjaxFrontend']) > 0) {
                $this->triggerHooksDeprecatedNotice();

                // Execute every registered callback
                foreach ($GLOBALS['TL_HOOKS']['simpleAjaxFrontend'] as $callback) {
                    if (is_array($callback)) {
                        System::importStatic($callback[0])->{$callback[1]}();
                    } elseif (is_callable($callback)) {
                        $callback();
                    }
                }
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
     *
     * @return void
     */
    private function triggerHooksDeprecatedNotice()
    {
        @trigger_error(
            'The Simple Ajax hooks are deprecated, use the event to listen for.',
            E_USER_DEPRECATED
        );
    }
}

<?php
/**
 * SimpleAjax extension for Contao Open Source CMS
 *
 * Copyright (c) 2016-2017 Richard Henkenjohann
 *
 * @package SimpleAjax
 * @author  Richard Henkenjohann <richardhenkenjohann@googlemail.com>
 */

namespace SimpleAjax\Event;


use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Response;


/**
 * Class SimpleAjax
 *
 * @package SimpleAjax\Event
 */
class SimpleAjax extends Event
{

    const NAME = 'contao.simpleajax';

    /**
     * Indicates whether the "SimpleAjaxFrontend.php" was called
     *
     * @var bool
     */
    protected $includeFrontendExclusive = false;

    /**
     * The response to send back to the client
     *
     * @var Response
     */
    protected $response;


    /**
     * SimpleAjax constructor.
     *
     * @param $includeFrontendExclusive
     */
    public function __construct($includeFrontendExclusive)
    {
        $this->includeFrontendExclusive = $includeFrontendExclusive;
    }

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
     * @param Response $response
     *
     * @return SimpleAjax
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;

        return $this;
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }
}

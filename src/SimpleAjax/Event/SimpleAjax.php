<?php
/**
 * SimpleAjax extension for Contao Open Source CMS
 *
 * Copyright (c) 2012 Leo Unglaub, 2016 Richard Henkenjohann
 *
 * @package SimpleAjax
 * @author  Richard Henkenjohann <richardhenkenjohann@googlemail.com>
 */

namespace SimpleAjax\Event;


use Symfony\Component\EventDispatcher\Event;


/**
 * Class SimpleAjax
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
}

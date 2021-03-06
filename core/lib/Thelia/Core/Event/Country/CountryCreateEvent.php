<?php

/*
 * This file is part of the Thelia package.
 * http://www.thelia.net
 *
 * (c) OpenStudio <info@thelia.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Thelia\Core\Event\Country;

/**
 * Class CountryCreateEvent.
 *
 * @author Manuel Raynaud <manu@raynaud.io>
 */
class CountryCreateEvent extends CountryEvent
{
    protected $locale;
    protected $title;
    protected $isocode;
    protected $isoAlpha2;
    protected $isoAlpha3;

    /** @var bool is visible */
    protected $visible;
    /** @var bool has states */
    protected $hasStates;

    protected $area;

    public function setIsoAlpha2($isoAlpha2)
    {
        $this->isoAlpha2 = $isoAlpha2;

        return $this;
    }

    public function getIsoAlpha2()
    {
        return $this->isoAlpha2;
    }

    public function setIsoAlpha3($isoAlpha3)
    {
        $this->isoAlpha3 = $isoAlpha3;

        return $this;
    }

    public function getIsoAlpha3()
    {
        return $this->isoAlpha3;
    }

    public function setIsocode($isocode)
    {
        $this->isocode = $isocode;

        return $this;
    }

    public function getIsocode()
    {
        return $this->isocode;
    }

    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    public function getLocale()
    {
        return $this->locale;
    }

    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param int $area
     */
    public function setArea($area)
    {
        $this->area = $area;

        return $this;
    }

    /**
     * @return int
     */
    public function getArea()
    {
        return $this->area;
    }

    /**
     * @return bool
     */
    public function isVisible()
    {
        return $this->visible;
    }

    /**
     * @param bool $visible
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;

        return $this;
    }

    /**
     * @return bool
     */
    public function isHasStates()
    {
        return $this->hasStates;
    }

    /**
     * @param bool $hasStates
     */
    public function setHasStates($hasStates)
    {
        $this->hasStates = $hasStates;

        return $this;
    }
}

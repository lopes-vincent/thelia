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

namespace Thelia\Core\Event\CustomerTitle;

use Thelia\Core\Event\ActionEvent;
use Thelia\Model\CustomerTitle;

/**
 * Class CustomerTitleEvent.
 *
 * @author Benjamin Perche <bperche@openstudio.fr>
 */
class CustomerTitleEvent extends ActionEvent
{
    /**
     * @var bool
     */
    protected $default = false;

    /**
     * @var string
     */
    protected $short;

    /**
     * @var string
     */
    protected $long;

    /**
     * @var string
     */
    protected $locale;

    /**
     * @var \Thelia\Model\CustomerTitle|null
     */
    protected $customerTitle;

    /**
     * @return \Thelia\Model\CustomerTitle
     */
    public function getCustomerTitle()
    {
        return $this->customerTitle;
    }

    /**
     * @return $this
     */
    public function setCustomerTitle(CustomerTitle $customerTitle = null)
    {
        $this->customerTitle = $customerTitle;

        return $this;
    }

    /**
     * @return bool
     */
    public function isDefault()
    {
        return $this->default;
    }

    /**
     * @param bool $default
     *
     * @return $this
     */
    public function setDefault($default)
    {
        $this->default = $default;

        return $this;
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @param string $locale
     *
     * @return $this
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * @return string
     */
    public function getLong()
    {
        return $this->long;
    }

    /**
     * @param string $long
     *
     * @return $this
     */
    public function setLong($long)
    {
        $this->long = $long;

        return $this;
    }

    /**
     * @return string
     */
    public function getShort()
    {
        return $this->short;
    }

    /**
     * @param string $short
     *
     * @return $this
     */
    public function setShort($short)
    {
        $this->short = $short;

        return $this;
    }
}

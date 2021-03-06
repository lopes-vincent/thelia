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

namespace Thelia\Core\Event\Customer;

use Thelia\Core\Event\ActionEvent;
use Thelia\Model\Customer;

/**
 * @deprecated since 2.4, please use \Thelia\Model\Event\CustomerEvent
 */
class CustomerEvent extends ActionEvent
{
    /** @var Customer|null */
    public $customer;

    public function __construct(Customer $customer = null)
    {
        $this->customer = $customer;
    }

    /**
     * @return $this
     */
    public function setCustomer(Customer $customer)
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * @return Customer|null
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @return bool
     */
    public function hasCustomer()
    {
        return $this->customer != null;
    }
}

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

namespace Thelia\Core\Event\Attribute;

use Thelia\Core\Event\ActionEvent;
use Thelia\Model\Attribute;

/**
 * @deprecated since 2.4, please use \Thelia\Model\Event\AttributeEvent
 */
class AttributeEvent extends ActionEvent
{
    protected $attribute;

    public function __construct(Attribute $attribute = null)
    {
        $this->attribute = $attribute;
    }

    public function hasAttribute()
    {
        return null !== $this->attribute;
    }

    public function getAttribute()
    {
        return $this->attribute;
    }

    public function setAttribute($attribute)
    {
        $this->attribute = $attribute;

        return $this;
    }
}

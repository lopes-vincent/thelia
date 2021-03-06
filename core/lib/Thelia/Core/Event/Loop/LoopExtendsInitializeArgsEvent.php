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

namespace Thelia\Core\Event\Loop;

use Thelia\Core\Template\Element\BaseLoop;

/**
 * Class LoopExtendsInitializeArgsEvent.
 *
 * @author Julien Chanséaume <julien@thelia.net>
 */
class LoopExtendsInitializeArgsEvent extends LoopExtendsEvent
{
    /** @var array the loop parameters when called. an array of name => value pairs */
    protected $loopParameters;

    /**
     * LoopExtendsInitializeArgs constructor.
     */
    public function __construct(BaseLoop $loop, array $loopParameters)
    {
        parent::__construct($loop);
        $this->loopParameters = $loopParameters;
    }

    /**
     * The loop parameters when called. an array of name => value pairs.
     *
     * @return array the loop parameters when called. an array of name => value pairs
     */
    public function getLoopParameters()
    {
        return $this->loopParameters;
    }

    /**
     * @param array $loopParameters
     */
    public function setLoopParameters($loopParameters)
    {
        $this->loopParameters = $loopParameters;

        return $this;
    }
}

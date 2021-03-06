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

namespace Thelia\Core\Event\MetaData;

/**
 * Class MetaDataCreateOrUpdateEvent.
 *
 * @author  Julien Chanséaume <jchanseaume@openstudio.fr>
 */
class MetaDataDeleteEvent extends MetaDataEvent
{
    /** @var string */
    protected $metaKey;

    /** @var string */
    protected $elementKey;

    /** @var int */
    protected $elementId;

    /**
     * MetaDataDeleteEvent constructor.
     *
     * @param string|null $metaKey
     * @param string|null $elementKey
     * @param int|null    $elementId
     */
    public function __construct($metaKey = null, $elementKey = null, $elementId = null)
    {
        parent::__construct();

        $this->metaKey = $metaKey;
        $this->elementKey = $elementKey;
        $this->elementId = $elementId;
    }

    public function setMetaKey(?string $metaKey)
    {
        $this->metaKey = $metaKey;

        return $this;
    }

    public function getMetaKey(): ?string
    {
        return $this->metaKey;
    }

    public function setElementKey(?string $elementKey)
    {
        $this->elementKey = $elementKey;

        return $this;
    }

    public function getElementKey(): ?string
    {
        return $this->elementKey;
    }

    public function setElementId(?int $elementId)
    {
        $this->elementId = $elementId;

        return $this;
    }

    public function getElementId(): ?int
    {
        return $this->elementId;
    }
}

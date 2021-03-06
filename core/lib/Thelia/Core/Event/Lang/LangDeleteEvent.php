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

namespace Thelia\Core\Event\Lang;

/**
 * Class LangDeleteEvent.
 *
 * @author Manuel Raynaud <manu@raynaud.io>
 */
class LangDeleteEvent extends LangEvent
{
    /**
     * @var int
     */
    protected $lang_id;

    /**
     * @param int $lang_id
     */
    public function __construct($lang_id)
    {
        $this->lang_id = $lang_id;
    }

    /**
     * @param int $lang_id
     *
     * @return $this
     */
    public function setLangId($lang_id)
    {
        $this->lang_id = $lang_id;

        return $this;
    }

    /**
     * @return int
     */
    public function getLangId()
    {
        return $this->lang_id;
    }
}

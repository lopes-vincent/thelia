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

namespace Thelia\Core\Event\Category;

use Thelia\Model\Category;

class CategoryDeleteContentEvent extends CategoryEvent
{
    protected $content_id;

    public function __construct(Category $category, $content_id)
    {
        parent::__construct($category);

        $this->content_id = $content_id;
    }

    public function getContentId()
    {
        return $this->content_id;
    }

    public function setContentId($content_id): void
    {
        $this->content_id = $content_id;
    }
}

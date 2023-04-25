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

namespace Thelia\Api\Resource;

use Symfony\Component\Serializer\Annotation\Groups;


class CategoryI18n extends I18n
{
    #[Groups([Category::GROUP_READ, Category::GROUP_WRITE, Product::GROUP_READ_SINGLE])]
    protected ?string $title;

    #[Groups([Category::GROUP_READ, Category::GROUP_WRITE, Product::GROUP_READ_SINGLE])]
    protected ?string $chapo;

    #[Groups([Category::GROUP_READ, Category::GROUP_WRITE, Product::GROUP_READ_SINGLE])]
    protected ?string $description;

    #[Groups([Category::GROUP_READ, Category::GROUP_WRITE])]
    protected ?string $postscriptum;

    #[Groups([Category::GROUP_READ, Category::GROUP_WRITE])]
    protected ?string $metaTitle;

    #[Groups([Category::GROUP_READ, Category::GROUP_WRITE])]
    protected ?string $metaDescription;

    #[Groups([Category::GROUP_READ, Category::GROUP_WRITE])]
    protected ?string $metaKeywords;

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): CategoryI18n
    {
        $this->title = $title;
        return $this;
    }

    public function getChapo(): ?string
    {
        return $this->chapo;
    }

    public function setChapo(?string $chapo): CategoryI18n
    {
        $this->chapo = $chapo;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): CategoryI18n
    {
        $this->description = $description;
        return $this;
    }

    public function getPostscriptum(): ?string
    {
        return $this->postscriptum;
    }

    public function setPostscriptum(?string $postscriptum): CategoryI18n
    {
        $this->postscriptum = $postscriptum;
        return $this;
    }

    public function getMetaTitle(): ?string
    {
        return $this->metaTitle;
    }

    public function setMetaTitle(?string $metaTitle): CategoryI18n
    {
        $this->metaTitle = $metaTitle;
        return $this;
    }

    public function getMetaDescription(): ?string
    {
        return $this->metaDescription;
    }

    public function setMetaDescription(?string $metaDescription): CategoryI18n
    {
        $this->metaDescription = $metaDescription;
        return $this;
    }

    public function getMetaKeywords(): ?string
    {
        return $this->metaKeywords;
    }

    public function setMetaKeywords(?string $metaKeywords): CategoryI18n
    {
        $this->metaKeywords = $metaKeywords;
        return $this;
    }
}
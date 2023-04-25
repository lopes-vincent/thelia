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

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use DateTime;
use Symfony\Component\Serializer\Annotation\Groups;
use Thelia\Api\Bridge\Propel\Attribute\Relation;

#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/admin/categories'
        ),
        new GetCollection(
            uriTemplate: '/admin/categories'
        ),
        new Get(
            uriTemplate: '/admin/categories/{id}'
        ),
        new Put(
            uriTemplate: '/admin/categories/{id}'
        ),
        new Delete(
            uriTemplate: '/admin/categories/{id}'
        )
    ],
    normalizationContext: ['groups' => [self::GROUP_READ]],
    denormalizationContext: ['groups' => [self::GROUP_WRITE]]
)]
class Category extends AbstractTranslatableResource
{
    public const GROUP_READ = 'category:read';
    public const GROUP_READ_SINGLE = 'category:read:single';
    public const GROUP_WRITE = 'category:write';

    #[Groups([self::GROUP_READ, Product::GROUP_READ_SINGLE, ProductCategory::GROUP_READ])]
    public ?int $id = null;

    #[Groups([self::GROUP_READ, self::GROUP_WRITE])]
    public int $parent;

    #[Groups([self::GROUP_READ, self::GROUP_WRITE])]
    public bool $visible;

    #[Groups([self::GROUP_READ, self::GROUP_WRITE])]
    public ?int $position;

    #[Groups([self::GROUP_READ, self::GROUP_WRITE])]
    public ?int $defaultTemplateId;

    #[Groups([self::GROUP_READ])]
    public ?DateTime $createdAt;

    #[Groups([self::GROUP_READ])]
    public ?DateTime $updatedAt;

    #[Groups([self::GROUP_READ, self::GROUP_WRITE])]
    public I18nCollection $i18ns;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): Category
    {
        $this->id = $id;
        return $this;
    }

    public function getParent(): int
    {
        return $this->parent;
    }

    public function setParent(int $parent): Category
    {
        $this->parent = $parent;
        return $this;
    }

    public function isVisible(): bool
    {
        return $this->visible;
    }

    public function setVisible(bool $visible): Category
    {
        $this->visible = $visible;
        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): Category
    {
        $this->position = $position;
        return $this;
    }

    public function getDefaultTemplateId(): ?int
    {
        return $this->defaultTemplateId;
    }

    public function setDefaultTemplateId(?int $defaultTemplateId): Category
    {
        $this->defaultTemplateId = $defaultTemplateId;
        return $this;
    }

    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?DateTime $createdAt): Category
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?DateTime $updatedAt): Category
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public static function getPropelModelClass(): string
    {
        return \Thelia\Model\Category::class;
    }

    public static function getI18nResourceClass(): string
    {
        return CategoryI18n::class;
    }
}
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
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/admin/brands'
        ),
        new GetCollection(
            uriTemplate: '/admin/brands'
        ),
        new Get(
            uriTemplate: '/admin/brands/{id}'
        ),
        new Put(
            uriTemplate: '/admin/brands/{id}'
        ),
        new Delete(
            uriTemplate: '/admin/brands/{id}'
        )
    ],
    normalizationContext: ['groups' => [self::GROUP_READ, I18n::GROUP_READ]],
    denormalizationContext: ['groups' => [self::GROUP_WRITE, I18n::GROUP_WRITE]]
)]
class Brand extends AbstractTranslatableResource
{
    public const GROUP_READ = 'brand:read';
    public const GROUP_READ_SINGLE = 'brand:read:single';
    public const GROUP_WRITE = 'brand:write';

    #[Groups([self::GROUP_READ, Product::GROUP_READ_SINGLE, Product::GROUP_WRITE])]
    public ?int $id = null;

    #[Groups([self::GROUP_READ, self::GROUP_WRITE])]
    public bool $visible;

    #[Groups([self::GROUP_READ, Product::GROUP_READ_SINGLE])]
    public ?int $position = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function isVisible(): bool
    {
        return $this->visible;
    }

    public function setVisible(bool $visible): self
    {
        $this->visible = $visible;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): Brand
    {
        $this->position = $position;
        return $this;
    }

    public static function getPropelModelClass(): string
    {
        return \Thelia\Model\Brand::class;
    }

    public static function getI18nResourceClass(): string
    {
        return BrandI18n::class;
    }
}

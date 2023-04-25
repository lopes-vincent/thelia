<?php

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
            uriTemplate: '/admin/content_images'
        ),
        new GetCollection(
            uriTemplate: '/admin/content_images'
        ),
        new Get(
            uriTemplate: '/admin/content_images/{id}'
        ),
        new Put(
            uriTemplate: '/admin/content_images/{id}'
        ),
        new Delete(
            uriTemplate: '/admin/content_images/{id}'
        )
    ],
    normalizationContext: ['groups' => [self::GROUP_READ]],
    denormalizationContext: ['groups' => [self::GROUP_WRITE]]
)]
class ContentImage extends AbstractTranslatableResource
{
    public const GROUP_READ = 'content_image:read';
    public const GROUP_READ_SINGLE = 'content_image:read:single';
    public const GROUP_WRITE = 'content_image:write';

    #[Groups([self::GROUP_READ])]
    public ?int $id = null;

    #[Relation(targetResource: Folder::class)]
    #[Groups([self::GROUP_READ])]
    public Content $content;

    #[Groups([self::GROUP_READ, self::GROUP_WRITE])]
    public string $file;

    #[Groups([self::GROUP_READ, self::GROUP_WRITE])]
    public bool $visible;

    #[Groups([self::GROUP_READ, self::GROUP_WRITE])]
    public ?int $position;

    #[Groups([self::GROUP_READ])]
    public ?DateTime $createdAt;

    #[Groups([self::GROUP_READ])]
    public ?DateTime $updatedAt;

    #[Groups([self::GROUP_READ, self::GROUP_WRITE])]
    public I18nCollection $i18ns;

    public static function getPropelModelClass(): string
    {
        return \Thelia\Model\ContentImage::class;
    }

    public static function getI18nResourceClass(): string
    {
        return ContentImageI18n::class;
    }
}
<?php

namespace Thelia\Api\Controller\Admin;

use ApiPlatform\Metadata\Post;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Thelia\Api\Bridge\Propel\Service\ApiResourceService;
use Thelia\Api\Bridge\Propel\Service\ItemFileResourceService;
use Thelia\Api\Resource\ItemFileResourceInterface;
use Thelia\Api\Resource\PropelResourceInterface;

#[AsController]
class PostItemFileController
{
    public function __invoke(
        Request $request,
        ItemFileResourceService $itemDocumentResourceService,
        ApiResourceService $apiResourceService
    )
    {
        /** @var ItemFileResourceInterface|PropelResourceInterface $resourceClass */
        $resourceClass = $request->get('_api_resource_class');

        if (!in_array(ItemFileResourceInterface::class, class_implements($resourceClass))) {
            throw new \Exception("Resource must implements ItemFileResourceInterface to use the PostItemFileController");
        }

        $itemId = $request->get($resourceClass::getItemType());

        $modelTableMap = $resourceClass::getPropelRelatedTableMap();
        $modelClassName = $modelTableMap->getClassName();
        $propelModel = new $modelClassName();
        $itemDocumentResourceService->createItemFile(
            $itemId,
            $propelModel
        );

        /** @var Post $operation */
        $operation = $request->get('_api_operation');

        return $apiResourceService->modelToResource(
            $resourceClass,
            $propelModel,
            $operation->getDenormalizationContext(),
        );
    }
}
<?php

namespace Thelia\Api\Bridge\Routing;

use ApiPlatform\Api\IriConverterInterface;
use ApiPlatform\Api\UrlGeneratorInterface;
use ApiPlatform\Exception\InvalidArgumentException;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Util\ClassInfoTrait;
use Symfony\Component\Routing\Exception\ExceptionInterface as RoutingExceptionInterface;
use Symfony\Component\Routing\RouterInterface;
use Thelia\Api\Attribute\CompositeIdentifiers;

class IriConverter implements IriConverterInterface
{
    use ClassInfoTrait;

    public function __construct(private IriConverterInterface $decorated, private readonly RouterInterface $router)
    {
    }

    public function getResourceFromIri(string $iri, array $context = [], ?Operation $operation = null): object
    {
        return $this->decorated->getResourceFromIri($iri, $context, $operation);
    }

    public function getIriFromResource(object|string $resource, int $referenceType = UrlGeneratorInterface::ABS_PATH, ?Operation $operation = null, array $context = []): ?string
    {
        $reflector = new \ReflectionClass($resource);

        $compositeIdentifiers = $reflector->getAttributes(CompositeIdentifiers::class);

        if (!empty($compositeIdentifiers)) {
            try {
                $identifiers = array_reduce(
                    $compositeIdentifiers[0]->getArguments()[0],
                    function ($carry, $identifier) use ($resource) {
                        $getter = 'get'.ucfirst($identifier);
                        $carry[$identifier] = $resource->$getter()->getId();
                        return $carry;
                    },
                    []
                );
                return $this->router->generate($operation->getName(), $identifiers, $operation->getUrlGenerationStrategy() ?? $referenceType);
            } catch (RoutingExceptionInterface $e) {
                // try not decorated converter
            }
        }

        return $this->decorated->getIriFromResource($resource, $referenceType, $operation, $context);
    }
}
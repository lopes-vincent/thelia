<?php

namespace Thelia\Api\Bridge\Propel\MetaData\Property;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\Property\Factory\PropertyMetadataFactoryInterface;
use Symfony\Component\PropertyInfo\Type;
use Thelia\Api\Bridge\Propel\Attribute\Relation;
use Thelia\Api\Resource\Brand;
use Thelia\Api\Resource\TranslatableResourceInterface;

class PropelPropertyMetadataFactory implements PropertyMetadataFactoryInterface
{
    public function __construct(private readonly PropertyMetadataFactoryInterface $decorated)
    {
    }

    public function create(string $resourceClass, string $property, array $options = []): ApiProperty
    {
        $propertyMetadata = $this->decorated->create($resourceClass, $property, $options);

        if ($propertyMetadata->getBuiltinTypes()[0] && $propertyMetadata->getBuiltinTypes()[0]?->getClassName() === Relation::class) {
            $propertyMetadata = $propertyMetadata->withBuiltinTypes([new Type(builtinType: 'object', class: Brand::class)]);
        }

        if ("i18ns" === $property && is_subclass_of($resourceClass, TranslatableResourceInterface::class)) {
            $i18nReflect = new \ReflectionClass($resourceClass::getI18nResourceClass());

            // Todo check Groups to better fit example with reality
            $propertyMetadata = $propertyMetadata->withOpenapiContext([
                'type' => 'object',
                'example' => [
                    'en_US' => array_reduce(
                        $i18nReflect->getProperties(),
                        function(array $carry, \ReflectionProperty $property) {
                            if ('id' === $property->getName()) {
                                return $carry;
                            }

                            $carry[$property->getName()] = 'en_US '.$property->getName();

                            return $carry;
                        },
                        []
                    ),
                    'fr_FR' => array_reduce(
                        $i18nReflect->getProperties(),
                        function(array $carry, \ReflectionProperty $property) {
                            if ('id' === $property->getName()) {
                                return $carry;
                            }

                            $carry[$property->getName()] = 'fr_FR '.$property->getName();

                            return $carry;
                        },
                        []
                    )
                ]
            ]);
        }

        return $propertyMetadata;
    }
}

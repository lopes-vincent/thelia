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

namespace TheliaMigrateCountry\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Thelia\Core\Form\Type\AbstractTheliaType;
use Thelia\Core\Translation\Translator;
use Thelia\Model\StateQuery;

/**
 * Class CountryStateMigrationType.
 *
 * @author Julien Chanséaume <julien@thelia.net>
 */
class CountryStateMigrationType extends AbstractTheliaType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'cascade_validation' => true,
                'constraints' => [
                    new Callback([$this, 'checkStateId']),
                ],
            ]
        );
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('migrate', 'checkbox')
            ->add(
                'country',
                'country_id'
            )
            ->add(
                'new_country',
                'country_id'
            )
            ->add(
                'new_state',
                'state_id',
                [
                    'constraints' => [],
                ]
            )
        ;
    }

    public function checkStateId($value, ExecutionContextInterface $context): void
    {
        if ($value['migrate']) {
            if (null !== $state = StateQuery::create()->findPk($value['new_state'])) {
                if ($state->getCountryId() !== $value['new_country']) {
                    $context->addViolation(
                        Translator::getInstance()->trans(
                            "The state id '%id' does not belong to country id '%id_country'",
                            [
                                '%id' => $value['new_state'],
                                '%id_country' => $value['new_country'],
                            ]
                        )
                    );
                }
            } else {
                $context->addViolation(
                    Translator::getInstance()->trans(
                        "The state id '%id' doesn't exist",
                        ['%id' => $value['new_state']]
                    )
                );
            }
        }
    }

    private function getRowData(ExecutionContextInterface $context): void
    {
        $propertyPath = $context->getPropertyPath();
        $data = $this->getRowData($context);
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'country_state_migration';
    }
}

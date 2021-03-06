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

namespace TheliaMigrateCountry\Form;

use Symfony\Component\Validator\Constraints\Count;
use Thelia\Form\BaseForm;

/**
 * Class CountryStateMigrationForm.
 *
 * @author Julien Chanséaume <julien@thelia.net>
 */
class CountryStateMigrationForm extends BaseForm
{
    /**
     * {@inheritDoc}
     */
    protected function buildForm(): void
    {
        $this->formBuilder
            ->add(
                'migrations',
                'collection',
                [
                    'type' => 'country_state_migration',
                    'allow_add' => true,
                    'required' => true,
                    'cascade_validation' => true,
                    'constraints' => [
                        new Count(['min' => 1]),
                    ],
                ]
            )
        ;
    }

    public static function getName()
    {
        return 'thelia_country_state_migration';
    }
}

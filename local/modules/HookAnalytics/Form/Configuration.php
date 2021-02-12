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

namespace HookAnalytics\Form;

use Thelia\Core\Translation\Translator;
use Thelia\Form\BaseForm;
use Thelia\Model\ConfigQuery;

/**
 * Class Configuration.
 *
 * @author Julien Chanséaume <jchanseaume@openstudio.fr>
 */
class Configuration extends BaseForm
{
    protected function buildForm()
    {
        $form = $this->formBuilder;

        $value = ConfigQuery::read('hookanalytics_trackingcode', '');
        $form->add(
            'trackingcode',
            'text',
            [
                'data' => $value,
                'label' => Translator::getInstance()->trans('Tracking Code'),
                'label_attr' => [
                    'for' => 'trackingcode',
                ],
            ]
        );
    }

    /**
     * @return string the name of you form. This name must be unique
     */
    public static function getName()
    {
        return 'hookanalytics';
    }
}

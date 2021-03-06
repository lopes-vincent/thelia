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

namespace Thelia\Form;

use Thelia\Form\Image\ImageModification;

/**
 * Created by JetBrains PhpStorm.
 * Date: 9/18/13
 * Time: 3:56 PM.
 *
 * Form allowing to process an image collection
 *
 * @author  Guillaume MOREL <gmorel@openstudio.fr>
 */
class ProductImageModification extends ImageModification
{
    /**
     * Get form name
     * This name must be unique.
     *
     * @return string
     */
    public static function getName()
    {
        return 'thelia_product_image_modification';
    }
}

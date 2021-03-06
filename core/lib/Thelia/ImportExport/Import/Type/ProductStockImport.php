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

namespace Thelia\ImportExport\Import\Type;

use Thelia\Core\Translation\Translator;
use Thelia\ImportExport\Import\AbstractImport;
use Thelia\Model\ProductSaleElementsQuery;

/**
 * Class ControllerTestBase.
 *
 * @author Jérôme Billiras <jbilliras@openstudio.fr>
 */
class ProductStockImport extends AbstractImport
{
    protected $mandatoryColumns = [
        'id',
        'stock',
    ];

    public function importData(array $data)
    {
        $pse = ProductSaleElementsQuery::create()->findPk($data['id']);

        if ($pse === null) {
            return Translator::getInstance()->trans(
                'The product sale element id %id doesn\'t exist',
                [
                    '%id' => $data['id'],
                ]
            );
        }
        $pse->setQuantity($data['stock']);

        if (isset($data['ean']) && !empty($data['ean'])) {
            $pse->setEanCode($data['ean']);
        }

        $pse->save();
        ++$this->importedRows;

        return null;
    }
}

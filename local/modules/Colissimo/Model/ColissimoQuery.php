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

namespace Colissimo\Model;

use Colissimo\Colissimo;
use Propel\Runtime\ActiveQuery\Criteria;
use Thelia\Model\OrderQuery;
use Thelia\Model\OrderStatus;
use Thelia\Model\OrderStatusQuery;

/**
 * Class ColissimoQuery.
 *
 * @author Manuel Raynaud <manu@raynaud.io>
 */
class ColissimoQuery
{
    /**
     * @return OrderQuery
     */
    public static function getOrders()
    {
        $status = OrderStatusQuery::create()
            ->filterByCode(
                [
                    OrderStatus::CODE_PAID,
                    OrderStatus::CODE_PROCESSING,
                ],
                Criteria::IN
            )
            ->find()
            ->toArray('code');

        $query = OrderQuery::create()
            ->filterByDeliveryModuleId((new Colissimo())->getModuleModel()->getId())
            ->filterByStatusId(
                [
                    $status[OrderStatus::CODE_PAID]['Id'],
                    $status[OrderStatus::CODE_PROCESSING]['Id'], ],
                Criteria::IN
            );

        return $query;
    }
}

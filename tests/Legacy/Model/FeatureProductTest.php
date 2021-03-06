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

namespace Thelia\Tests\Model;

use PHPUnit\Framework\TestCase;
use Propel\Runtime\Propel;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Thelia\Action\Product;
use Thelia\Model\CategoryQuery;
use Thelia\Model\FeatureAvQuery;
use Thelia\Model\FeatureProductQuery;

/**
 * Class ProductTest.
 */
class FeatureProductTest extends TestCase
{
    public function getContainer()
    {
        $container = new \Symfony\Component\DependencyInjection\ContainerBuilder();

        return $container;
    }

    public function getEventDispatcher()
    {
        $eventDispatcher = new EventDispatcher();

        $eventDispatcher->addSubscriber(new Product($eventDispatcher));

        return $eventDispatcher;
    }

    public function testProductDeleteFreeTextFeatureAv(): void
    {
        $con = Propel::getConnection();
        $con->beginTransaction();

        $featureProduct = FeatureProductQuery::create($con)->findOneByIsFreeText(true);
        $featureAvId = $featureProduct->getFeatureAvId();
        $this->assertNotNull($featureAvId, '`feature_av_id` in `feature_product` table cannot be null');

        $featureProduct
            ->getProduct()
            ->delete($con);

        $featureAv = FeatureAvQuery::create($con)->findPk($featureAvId);
        $this->assertNull($featureAv, 'Free text feature av does not deleted on product deletion');

        $con->rollback();
    }

    public function testCategoryDeleteFreeTextFeatureAv(): void
    {
        $con = Propel::getConnection();
        $con->beginTransaction();

        $featureProduct = FeatureProductQuery::create($con)->findOneByIsFreeText(true);
        $featureAvId = $featureProduct->getFeatureAvId();
        $this->assertNotNull($featureAvId, '`feature_av_id` in `feature_product` table cannot be null');

        CategoryQuery::create()
            ->findPk($featureProduct->getProduct()->getDefaultCategoryId())
            ->delete($con);

        $featureAv = FeatureAvQuery::create($con)->findPk($featureAvId);
        $this->assertNull($featureAv, 'Free text feature av does not deleted on category deletion');

        $con->rollback();
    }
}

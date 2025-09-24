<?php

declare(strict_types = 1);

namespace SprykerCommunity\Zed\ProductSubscription\Persistence;

use Orm\Zed\ProductSubscription\Persistence\SpyProductSubscriptionQuery;
use Spryker\Zed\Kernel\Persistence\QueryContainer\QueryContainerInterface;

interface ProductSubscriptionQueryContainerInterface extends QueryContainerInterface
{
    /**
     * @api
     *
     * @return \Orm\Zed\ProductSubscription\Persistence\SpyProductSubscriptionQuery
     */
    public function queryRenewableSubscriptions(): SpyProductSubscriptionQuery;
}

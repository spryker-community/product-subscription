<?php

declare(strict_types = 1);

namespace SprykerCommunity\Zed\ProductSubscription\Persistence;

use Orm\Zed\ProductSubscription\Persistence\SpyProductSubscriptionQuery;
use Spryker\Zed\Kernel\Persistence\AbstractQueryContainer;

/**
 * @method \SprykerCommunity\Zed\ProductSubscription\Persistence\ProductSubscriptionPersistenceFactory getFactory()
 */
class ProductSubscriptionQueryContainer extends AbstractQueryContainer implements ProductSubscriptionQueryContainerInterface
{
    /**
     * @api
     *
     * @return \Orm\Zed\ProductSubscription\Persistence\SpyProductSubscriptionQuery
     */
    public function queryRenewableSubscriptions(): SpyProductSubscriptionQuery
    {
        return $this->getFactory()
            ->createProductSubscriptionQuery();
        // Todo Example filter about renew_subscription
    }
}

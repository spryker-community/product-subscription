<?php

declare(strict_types = 1);

namespace SprykerCommunity\Zed\ProductSubscription\Business;
class ProductSubscriptionRenewer implements ProductSubscriptionRenewerInterface
{
    public function renew(): void
    {
        // Select all items that have next_schedule in the past
        // create a new order maybe delayed for a little later time
        // Let the OMS handle the order
    }
}

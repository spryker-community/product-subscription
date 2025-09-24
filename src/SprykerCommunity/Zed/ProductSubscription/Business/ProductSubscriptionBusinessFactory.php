<?php

declare(strict_types = 1);

namespace SprykerCommunity\Zed\ProductSubscription\Business;

use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;

class ProductSubscriptionBusinessFactory extends AbstractBusinessFactory
{
    public function createProductSubscriptionRenewer(): ProductSubscriptionRenewerInterface
    {
        return new ProductSubscriptionRenewer();
    }
}

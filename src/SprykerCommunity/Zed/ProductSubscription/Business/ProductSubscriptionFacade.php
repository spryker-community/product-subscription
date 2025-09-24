<?php

declare(strict_types = 1);

namespace SprykerCommunity\Zed\ProductSubscription\Business;

use Spryker\Zed\Kernel\Business\AbstractFacade;

class ProductSubscriptionFacade extends AbstractFacade implements ProductSubscriptionFacadeInterface
{
    public function renewSubscriptions(): void
    {
        $this->getFactory()
            ->createProductSubscriptionRenewer()
            ->renewSubscriptions();
    }
}

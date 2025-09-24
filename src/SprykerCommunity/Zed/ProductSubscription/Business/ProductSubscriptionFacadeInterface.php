<?php

declare(strict_types = 1);

namespace SprykerCommunity\Zed\ProductSubscription\Business;

interface ProductSubscriptionFacadeInterface
{
    public function renewSubscriptions(): void;
}

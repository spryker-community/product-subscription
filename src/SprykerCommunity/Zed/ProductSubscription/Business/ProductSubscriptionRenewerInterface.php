<?php

declare(strict_types = 1);

namespace SprykerCommunity\Zed\ProductSubscription\Business;

interface ProductSubscriptionRenewerInterface
{
    public function renew(): void;
}

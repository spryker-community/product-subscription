<?php

declare(strict_types = 1);

namespace SprykerCommunity\Zed\ProductSubscription\Business;

use DateInterval;
use DateTime;
use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\ProductSubscriptionTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\TotalsTransfer;
use Spryker\Zed\Checkout\Business\CheckoutFacadeInterface;
use Spryker\Zed\Customer\Business\CustomerFacadeInterface;
use Spryker\Zed\Quote\Business\QuoteFacadeInterface;
use SprykerCommunity\Zed\ProductSubscription\Persistence\ProductSubscriptionEntityManagerInterface;
use SprykerCommunity\Zed\ProductSubscription\Persistence\ProductSubscriptionRepositoryInterface;

class ProductSubscriptionRenewer implements ProductSubscriptionRenewerInterface
{
    protected ProductSubscriptionRepositoryInterface $productSubscriptionRepository;

    protected ProductSubscriptionEntityManagerInterface $productSubscriptionEntityManager;

    protected QuoteFacadeInterface $quoteFacade;

    protected CheckoutFacadeInterface $checkoutFacade;

    protected CustomerFacadeInterface $customerFacade;

    /**
     * @param \SprykerCommunity\Zed\ProductSubscription\Persistence\ProductSubscriptionRepositoryInterface $productSubscriptionRepository
     * @param \SprykerCommunity\Zed\ProductSubscription\Persistence\ProductSubscriptionEntityManagerInterface $productSubscriptionEntityManager
     * @param \Spryker\Zed\Quote\Business\QuoteFacadeInterface $quoteFacade
     * @param \Spryker\Zed\Checkout\Business\CheckoutFacadeInterface $checkoutFacade
     * @param \Spryker\Zed\Customer\Business\CustomerFacadeInterface $customerFacade
     */
    public function __construct(
        ProductSubscriptionRepositoryInterface $productSubscriptionRepository,
        ProductSubscriptionEntityManagerInterface $productSubscriptionEntityManager,
        QuoteFacadeInterface $quoteFacade,
        CheckoutFacadeInterface $checkoutFacade,
        CustomerFacadeInterface $customerFacade,
    ) {
        $this->productSubscriptionRepository = $productSubscriptionRepository;
        $this->productSubscriptionEntityManager = $productSubscriptionEntityManager;
        $this->quoteFacade = $quoteFacade;
        $this->checkoutFacade = $checkoutFacade;
        $this->customerFacade = $customerFacade;
    }

    /**
     * Finds all subscriptions due for renewal, creates a new order for each,
     * and schedules the next renewal.
     *
     * @return void
     */
    public function renew(): void
    {
        // 1. Select all items that have `next_renewal_at` in the past
        $renewableSubscriptions = $this->productSubscriptionRepository->findRenewableSubscriptions();

        foreach ($renewableSubscriptions as $subscriptionTransfer) {
            // 2. Prepare a new quote for the subscription
            $quoteTransfer = $this->createQuoteFromSubscription($subscriptionTransfer);

            if ($quoteTransfer === null) {
                // Could not find customer or other essential data, skip this subscription.
                // Consider adding logging here.
                continue;
            }

            // 3. Place the order
            $checkoutResponse = $this->checkoutFacade->placeOrder($quoteTransfer);

            // 4. Update subscription if order was successful
            if (!$checkoutResponse->getIsSuccess()) {
                continue;
            }

            $this->scheduleNextRenewal($subscriptionTransfer);
        }
    }

    /**
     * Creates and prepares a QuoteTransfer from a ProductSubscriptionTransfer.
     *
     * @param \Generated\Shared\Transfer\ProductSubscriptionTransfer $subscriptionTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer|null
     */
    protected function createQuoteFromSubscription(
        ProductSubscriptionTransfer $subscriptionTransfer,
    ): ?QuoteTransfer {
        $customerTransfer = (new CustomerTransfer())->setIdCustomer($subscriptionTransfer->getFkCustomer());
        $customerResponse = $this->customerFacade->getCustomer($customerTransfer);
        $customer = $customerResponse->getCustomerTransfer();

        if (!$customer) {
            return null; // Customer not found
        }

        // Create a new quote and set the customer
        $quoteTransfer = (new QuoteTransfer())->setCustomer($customer);
        $quoteResponse = $this->quoteFacade->createQuote($quoteTransfer);
        $quoteTransfer = $quoteResponse->getQuoteTransfer();

        // Add the subscription product to the quote
        $itemTransfer = (new ItemTransfer())
            ->setSku($subscriptionTransfer->getSku())
            ->setQuantity(1)
            ->setUnitPriceToPayAggregation($subscriptionTransfer->getPrice()); // Use stored price

        $this->quoteFacade->addItem($quoteTransfer, $itemTransfer);

        // Set addresses, payment, shipment from original order or customer profile
        // This part needs to be adapted to your project's logic for selecting these details.
        // For example, using the customer's default addresses:
        $quoteTransfer->setBillingAddress($customer->getDefaultBillingAddress());
        $quoteTransfer->setShippingAddress($customer->getDefaultShippingAddress());

        // Recalculate totals
        $quoteTransfer->setTotals(new TotalsTransfer());
        $this->quoteFacade->recalculateQuote($quoteTransfer);

        return $quoteTransfer;
    }

    /**
     * Calculates and persists the next renewal date for a subscription.
     *
     * @param \Generated\Shared\Transfer\ProductSubscriptionTransfer $subscriptionTransfer
     *
     * @return void
     */
    protected function scheduleNextRenewal(
        ProductSubscriptionTransfer $subscriptionTransfer,
    ): void {
        $interval = new DateInterval($subscriptionTransfer->getInterval());
        $nextRenewalDate = (new DateTime())->add($interval);

        $subscriptionTransfer->setNextRenewalAt($nextRenewalDate->format('Y-m-d H:i:s'));

        $this->productSubscriptionEntityManager->saveProductSubscription($subscriptionTransfer);
    }
}

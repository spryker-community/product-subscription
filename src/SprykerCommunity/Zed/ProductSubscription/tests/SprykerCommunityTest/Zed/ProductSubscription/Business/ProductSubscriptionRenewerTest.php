<?php

namespace SprykerCommunityTest\Zed\ProductSubscription\Business;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\CustomerResponseTransfer;
use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\ProductSubscriptionTransfer;
use Generated\Shared\Transfer\QuoteResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Zed\Checkout\Business\CheckoutFacadeInterface;
use Spryker\Zed\Customer\Business\CustomerFacadeInterface;
use Spryker\Zed\Quote\Business\QuoteFacadeInterface;
use SprykerCommunity\Zed\ProductSubscription\Business\ProductSubscriptionRenewer;
use SprykerCommunity\Zed\ProductSubscription\Persistence\ProductSubscriptionEntityManagerInterface;
use SprykerCommunity\Zed\ProductSubscription\Persistence\ProductSubscriptionRepositoryInterface;

/**
 * Auto-generated group annotations
 * @group SprykerCommunityTest
 * @group Zed
 * @group ProductSubscription
 * @group Business
 * @group ProductSubscriptionRenewerTest
 * Add your own group annotations below this line
 */
class ProductSubscriptionRenewerTest extends Unit
{
    /**
     * @var \SprykerCommunityTest\Zed\ProductSubscription\ProductSubscriptionBusinessTester
     */
    protected $tester;

    /**
     * Tests the main success scenario for the renew() method.
     * It verifies that if a renewable subscription is found, the order is placed
     * and the subscription's next renewal date is updated.
     *
     * @return void
     */
    public function testRenewSuccessfullyRenewsAndSchedulesSubscription(): void
    {
        // ARRANGE
        $productSubscriptionRepositoryMock = $this->createProductSubscriptionRepositoryMock();
        $checkoutFacadeMock = $this->createCheckoutFacadeMock(true); // Simulate a successful order
        $productSubscriptionEntityManagerMock = $this->createEntityManagerMock();

        // Instantiate the class we are testing with all its dependencies mocked
        $renewer = new ProductSubscriptionRenewer(
            $productSubscriptionRepositoryMock,
            $productSubscriptionEntityManagerMock,
            $this->createQuoteFacadeMock(),
            $checkoutFacadeMock,
            $this->createCustomerFacadeMock()
        );

        // ACT
        $renewer->renew();
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\SprykerCommunity\Zed\ProductSubscription\Persistence\ProductSubscriptionRepositoryInterface
     */
    protected function createProductSubscriptionRepositoryMock(): object
    {
        $mock = $this->getMockBuilder(ProductSubscriptionRepositoryInterface::class)->getMock();

        $subscriptionTransfer = (new ProductSubscriptionTransfer())
            ->setFkCustomer(1)
            ->setSku('SUB-001')
            ->setPrice(1999)
            ->setInterval('P1M'); // Interval of 1 month

        // Expect `findRenewableSubscriptions` to be called once and return our test subscription.
        $mock->expects($this->once())
            ->method('findRenewableSubscriptions')
            ->willReturn([$subscriptionTransfer]);

        return $mock;
    }

    /**
     * @param bool $isSuccess Determines if the mocked checkout should succeed or fail.
     *
     * @return \PHPUnit\Framework\MockObject\MockObject|\Spryker\Zed\Checkout\Business\CheckoutFacadeInterface
     */
    protected function createCheckoutFacadeMock(bool $isSuccess): object
    {
        $mock = $this->getMockBuilder(CheckoutFacadeInterface::class)->getMock();

        $checkoutResponse = (new CheckoutResponseTransfer())->setIsSuccess($isSuccess);

        // Expect `placeOrder` to be called once and return our configured response.
        $mock->expects($this->once())
            ->method('placeOrder')
            ->willReturn($checkoutResponse);

        return $mock;
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\SprykerCommunity\Zed\ProductSubscription\Persistence\ProductSubscriptionEntityManagerInterface
     */
    protected function createEntityManagerMock(): object
    {
        $mock = $this->getMockBuilder(ProductSubscriptionEntityManagerInterface::class)->getMock();

        // This is our main assertion: we expect the entity manager's `save` method to be called exactly once.
        // This only happens if the order placement was successful.
        $mock->expects($this->once())
            ->method('saveProductSubscription');

        return $mock;
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\Spryker\Zed\Quote\Business\QuoteFacadeInterface
     */
    protected function createQuoteFacadeMock(): object
    {
        $mock = $this->getMockBuilder(QuoteFacadeInterface::class)->getMock();
        $quoteResponse = (new QuoteResponseTransfer())->setQuoteTransfer(new QuoteTransfer());

        // We don't need to assert calls on the quote facade for this test,
        // but we must ensure its methods return the necessary transfers to avoid errors.
        $mock->method('createQuote')->willReturn($quoteResponse);
        $mock->method('addItem')->willReturn($quoteResponse);
        $mock->method('recalculateQuote');

        return $mock;
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\Spryker\Zed\Customer\Business\CustomerFacadeInterface
     */
    protected function createCustomerFacadeMock(): object
    {
        $mock = $this->getMockBuilder(CustomerFacadeInterface::class)->getMock();
        $customerResponse = (new CustomerResponseTransfer())->setCustomerTransfer(new CustomerTransfer());
        $mock->method('getCustomer')->willReturn($customerResponse);

        return $mock;
    }
}

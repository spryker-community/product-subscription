<?php

declare(strict_types = 1);

namespace SprykerCommunity\Zed\ProductSubscription\Communication\Console;

use Spryker\Zed\Kernel\Communication\Console\Console;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RenewSubscriptionConsole extends Console
{
    public const COMMAND_NAME = 'product:subscription:renew';

    protected function configure(): void
    {
        parent::configure();

        $this->setName(static::COMMAND_NAME);
        $this->setDescription('Renews all subscriptions.');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->info('Renewing subscriptions...');

        $this->getFacade()->renewSubscriptions();

        return static::CODE_SUCCESS;
    }
}

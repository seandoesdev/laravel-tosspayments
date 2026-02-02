<?php

declare(strict_types=1);

namespace Seandoesdev\TossPayments\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use Seandoesdev\TossPayments\TossPaymentsServiceProvider;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            TossPaymentsServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app): array
    {
        return [
            'TossPayments' => \Seandoesdev\TossPayments\Facades\TossPayments::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('tosspayments.client_key', 'test_client_key');
        $app['config']->set('tosspayments.secret_key', 'test_secret_key');
        $app['config']->set('tosspayments.webhook.secret', 'test_webhook_secret');
    }
}

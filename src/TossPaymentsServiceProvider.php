<?php

declare(strict_types=1);

namespace Seandoesdev\TossPayments;

use Illuminate\Support\ServiceProvider;
use Seandoesdev\TossPayments\Contracts\PaymentClientInterface;
use Seandoesdev\TossPayments\Http\Middleware\VerifyTossWebhookSignature;
use Seandoesdev\TossPayments\Http\TossClient;
use Seandoesdev\TossPayments\Services\PaymentWidgetService;
use Seandoesdev\TossPayments\Support\WebhookVerifier;

class TossPaymentsServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/tosspayments.php',
            'tosspayments'
        );

        // TossClient 바인딩 (레거시 호환)
        $this->app->singleton(PaymentClientInterface::class, function ($app) {
            return new TossClient(
                secretKey: config('tosspayments.secret_key'),
                baseUrl: config('tosspayments.base_url'),
                version: config('tosspayments.version'),
                timeout: config('tosspayments.timeout'),
                retryConfig: config('tosspayments.retry'),
                sslVerify: config('tosspayments.ssl_verify'),
            );
        });

        // PaymentWidgetService 바인딩 (레거시 호환)
        $this->app->singleton(PaymentWidgetService::class, function ($app) {
            return new PaymentWidgetService(
                client: $app->make(PaymentClientInterface::class),
            );
        });

        // WebhookVerifier 바인딩
        $this->app->singleton(WebhookVerifier::class, function ($app) {
            return new WebhookVerifier(
                secretKey: config('tosspayments.webhook.secret'),
                tolerance: config('tosspayments.webhook.tolerance', 300),
            );
        });

        // 미들웨어 바인딩
        $this->app->singleton(VerifyTossWebhookSignature::class, function ($app) {
            return new VerifyTossWebhookSignature(
                verifier: $app->make(WebhookVerifier::class),
            );
        });

        // Facade 바인딩 (레거시 호환)
        $this->app->alias(PaymentWidgetService::class, 'tosspayments');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/tosspayments.php' => config_path('tosspayments.php'),
            ], 'tosspayments-config');
        }

        // 미들웨어 별칭 등록
        $router = $this->app['router'];
        $router->aliasMiddleware('tosspayments.webhook', VerifyTossWebhookSignature::class);
    }
}

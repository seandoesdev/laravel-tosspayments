<?php

declare(strict_types=1);

namespace Seandoesdev\TossPayments\Facades;

use Illuminate\Support\Facades\Facade;
use Seandoesdev\TossPayments\Attributes\Billing;
use Seandoesdev\TossPayments\Attributes\CashReceipt;
use Seandoesdev\TossPayments\Attributes\Payment;
use Seandoesdev\TossPayments\Attributes\Promotion;
use Seandoesdev\TossPayments\Attributes\Settlement;
use Seandoesdev\TossPayments\Attributes\Transaction;
use Seandoesdev\TossPayments\TossPayments as TossPaymentsClient;

/**
 * TossPayments Facade
 *
 * 새로운 방식 (getsolaris 스타일):
 * @method static Payment|Billing|CashReceipt|Transaction|Settlement|Promotion for(string $attributeClass)
 *
 * 레거시 방식 (PaymentWidgetService):
 * @method static \Seandoesdev\TossPayments\DataObjects\PaymentResult confirm(\Seandoesdev\TossPayments\DataObjects\PaymentConfirmData $data)
 * @method static \Seandoesdev\TossPayments\DataObjects\PaymentResult getPayment(string $paymentKey)
 * @method static \Seandoesdev\TossPayments\DataObjects\PaymentResult getPaymentByOrderId(string $orderId)
 * @method static \Seandoesdev\TossPayments\DataObjects\PaymentResult cancel(string $paymentKey, string $cancelReason, ?int $cancelAmount = null)
 *
 * @see \Seandoesdev\TossPayments\TossPayments
 * @see \Seandoesdev\TossPayments\Services\PaymentWidgetService
 */
class TossPayments extends Facade
{
    /**
     * 정적 팩토리 메서드 - Attribute 클래스 인스턴스 반환
     *
     * 사용 예시:
     * - TossPayments::for(Payment::class)->paymentKey($key)->confirm()
     * - TossPayments::for(Billing::class)->billingKey($key)->request()
     *
     * @template T
     * @param class-string<T> $attributeClass
     * @return T
     */
    public static function for(string $attributeClass): mixed
    {
        return TossPaymentsClient::for($attributeClass);
    }

    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'tosspayments';
    }
}

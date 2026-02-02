<?php

declare(strict_types=1);

namespace Seandoesdev\TossPayments\Services;

use Seandoesdev\TossPayments\Contracts\PaymentClientInterface;
use Seandoesdev\TossPayments\DataObjects\PaymentConfirmData;
use Seandoesdev\TossPayments\DataObjects\PaymentResult;

/**
 * 결제 위젯 서비스
 *
 * Toss Payments Widget API 연동을 담당합니다.
 */
class PaymentWidgetService
{
    public function __construct(
        private readonly PaymentClientInterface $client,
    ) {
    }

    public function for(string $modelClass): PaymentRequestBuilder
    {
        return new PaymentRequestBuilder(
            client: $this->client,
            modelClass: $modelClass,
        );
    }

    /**
     * 결제 승인
     *
     * 클라이언트에서 결제 인증 후 서버에서 승인을 요청합니다.
     *
     * @throws \Seandoesdev\TossPayments\Exceptions\TossValidationException
     * @throws \Seandoesdev\TossPayments\Exceptions\TossServerException
     */
    public function confirm(PaymentConfirmData $data): PaymentResult
    {
        $response = $this->client->request('POST', 'payments/confirm', $data->toArray());

        return PaymentResult::fromJson($response);
    }

    /**
     * 결제 조회
     *
     * paymentKey로 결제 정보를 조회합니다.
     *
     * @throws \Seandoesdev\TossPayments\Exceptions\TossValidationException
     * @throws \Seandoesdev\TossPayments\Exceptions\TossServerException
     */
    public function getPayment(string $paymentKey): PaymentResult
    {
        $response = $this->client->request('GET', "payments/{$paymentKey}");

        return PaymentResult::fromJson($response);
    }

    /**
     * 주문 ID로 결제 조회
     *
     * orderId로 결제 정보를 조회합니다.
     *
     * @throws \Seandoesdev\TossPayments\Exceptions\TossValidationException
     * @throws \Seandoesdev\TossPayments\Exceptions\TossServerException
     */
    public function getPaymentByOrderId(string $orderId): PaymentResult
    {
        $response = $this->client->request('GET', "payments/orders/{$orderId}");

        return PaymentResult::fromJson($response);
    }

    /**
     * 결제 취소
     *
     * 승인된 결제를 취소합니다.
     *
     * @param string $paymentKey 결제 키
     * @param string $cancelReason 취소 사유
     * @param int|null $cancelAmount 취소 금액 (부분 취소 시)
     *
     * @throws \Seandoesdev\TossPayments\Exceptions\TossValidationException
     * @throws \Seandoesdev\TossPayments\Exceptions\TossServerException
     */
    public function cancel(
        string $paymentKey,
        string $cancelReason,
        ?int $cancelAmount = null,
    ): PaymentResult {
        $data = ['cancelReason' => $cancelReason];

        if ($cancelAmount !== null) {
            $data['cancelAmount'] = $cancelAmount;
        }

        $response = $this->client->request('POST', "payments/{$paymentKey}/cancel", $data);

        return PaymentResult::fromJson($response);
    }
}

<?php

declare(strict_types=1);

namespace Seandoesdev\TossPayments\DataObjects;

use Seandoesdev\TossPayments\Enums\PaymentStatus;

/**
 * 결제 결과 응답 DTO
 *
 * @see https://docs.tosspayments.com/reference#payment-%EA%B0%9D%EC%B2%B4
 */
readonly class PaymentResult
{
    public function __construct(
        public string $paymentKey,
        public string $orderId,
        public string $orderName,
        public PaymentStatus $status,
        public int $totalAmount,
        public int $balanceAmount,
        public string $method,
        public ?string $approvedAt = null,
        public ?string $requestedAt = null,
        public ?string $receiptUrl = null,
        public ?array $card = null,
        public ?array $virtualAccount = null,
        public ?array $easyPay = null,
        public ?array $cancels = null,
        public ?array $failure = null,
        public array $raw = [],
    ) {
    }

    /**
     * API 응답 JSON으로부터 DTO 생성
     *
     * @param array<string, mixed> $json
     */
    public static function fromJson(array $json): self
    {
        return new self(
            paymentKey: $json['paymentKey'],
            orderId: $json['orderId'],
            orderName: $json['orderName'] ?? '',
            status: PaymentStatus::from($json['status']),
            totalAmount: (int) $json['totalAmount'],
            balanceAmount: (int) ($json['balanceAmount'] ?? $json['totalAmount']),
            method: $json['method'] ?? '',
            approvedAt: $json['approvedAt'] ?? null,
            requestedAt: $json['requestedAt'] ?? null,
            receiptUrl: $json['receipt']['url'] ?? null,
            card: $json['card'] ?? null,
            virtualAccount: $json['virtualAccount'] ?? null,
            easyPay: $json['easyPay'] ?? null,
            cancels: $json['cancels'] ?? null,
            failure: $json['failure'] ?? null,
            raw: $json,
        );
    }

    /**
     * 결제가 성공했는지 확인
     */
    public function isSuccessful(): bool
    {
        return $this->status->isCompleted();
    }

    /**
     * 결제가 취소되었는지 확인
     */
    public function isCanceled(): bool
    {
        return $this->status->isCanceled();
    }

    /**
     * 원본 응답 데이터 반환
     *
     * @return array<string, mixed>
     */
    public function getRawData(): array
    {
        return $this->raw;
    }

    /**
     * 배열로 변환
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'paymentKey' => $this->paymentKey,
            'orderId' => $this->orderId,
            'orderName' => $this->orderName,
            'status' => $this->status->value,
            'totalAmount' => $this->totalAmount,
            'balanceAmount' => $this->balanceAmount,
            'method' => $this->method,
            'approvedAt' => $this->approvedAt,
            'requestedAt' => $this->requestedAt,
            'receiptUrl' => $this->receiptUrl,
        ];
    }

    /**
     * JSON-friendly data for responses.
     *
     * @return array<string, mixed>
     */
    public function json(): array
    {
        return $this->toArray();
    }
}

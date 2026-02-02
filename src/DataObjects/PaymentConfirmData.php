<?php

declare(strict_types=1);

namespace Seandoesdev\TossPayments\DataObjects;

/**
 * 결제 승인 요청 DTO
 *
 * @see https://docs.tosspayments.com/reference#%EA%B2%B0%EC%A0%9C-%EC%8A%B9%EC%9D%B8
 */
readonly class PaymentConfirmData
{
    public function __construct(
        public string $paymentKey,
        public string $orderId,
        public int $amount,
    ) {
    }

    /**
     * 배열로부터 DTO 생성
     *
     * @param array{paymentKey: string, orderId: string, amount: int|string} $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            paymentKey: $data['paymentKey'],
            orderId: $data['orderId'],
            amount: (int) $data['amount'],
        );
    }

    /**
     * Request 객체로부터 DTO 생성
     */
    public static function fromRequest(\Illuminate\Http\Request $request): self
    {
        return self::fromArray([
            'paymentKey' => $request->input('paymentKey'),
            'orderId' => $request->input('orderId'),
            'amount' => $request->input('amount'),
        ]);
    }

    /**
     * API 요청 바디로 변환
     *
     * @return array{paymentKey: string, orderId: string, amount: int}
     */
    public function toArray(): array
    {
        return [
            'paymentKey' => $this->paymentKey,
            'orderId' => $this->orderId,
            'amount' => $this->amount,
        ];
    }
}

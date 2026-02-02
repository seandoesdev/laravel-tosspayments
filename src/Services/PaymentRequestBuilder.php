<?php

declare(strict_types=1);

namespace Seandoesdev\TossPayments\Services;

use InvalidArgumentException;
use Seandoesdev\TossPayments\Contracts\PaymentClientInterface;
use Seandoesdev\TossPayments\DataObjects\PaymentConfirmData;
use Seandoesdev\TossPayments\DataObjects\PaymentResult;

class PaymentRequestBuilder
{
    private ?string $paymentKey = null;
    private ?string $orderId = null;
    private ?int $amount = null;

    public function __construct(
        private readonly PaymentClientInterface $client,
        private readonly string $modelClass,
    ) {
    }

    public function paymentKey(string $paymentKey): self
    {
        $this->paymentKey = $paymentKey;

        return $this;
    }

    public function orderId(string $orderId): self
    {
        $this->orderId = $orderId;

        return $this;
    }

    public function amount(int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function confirm(): PaymentResult
    {
        if ($this->paymentKey === null || $this->orderId === null || $this->amount === null) {
            throw new InvalidArgumentException('paymentKey, orderId, amount are required');
        }

        $data = new PaymentConfirmData(
            paymentKey: $this->paymentKey,
            orderId: $this->orderId,
            amount: $this->amount,
        );

        $response = $this->client->request('POST', 'payments/confirm', $data->toArray());

        return PaymentResult::fromJson($response);
    }
}

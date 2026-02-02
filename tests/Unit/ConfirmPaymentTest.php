<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use Seandoesdev\TossPayments\DataObjects\PaymentConfirmData;
use Seandoesdev\TossPayments\Enums\PaymentStatus;
use Seandoesdev\TossPayments\Exceptions\TossValidationException;
use Seandoesdev\TossPayments\Facades\TossPayments;

beforeEach(function () {
    Http::preventStrayRequests();
});

describe('결제 승인 테스트', function () {
    it('결제 승인 성공', function () {
        Http::fake([
            'https://api.tosspayments.com/v2/payments/confirm' => Http::response([
                'paymentKey' => 'test_payment_key',
                'orderId' => 'test_order_123',
                'orderName' => '테스트 상품',
                'status' => 'DONE',
                'totalAmount' => 10000,
                'balanceAmount' => 10000,
                'method' => '카드',
                'approvedAt' => '2024-01-01T12:00:00+09:00',
                'requestedAt' => '2024-01-01T11:59:00+09:00',
            ], 200),
        ]);

        $data = PaymentConfirmData::fromArray([
            'paymentKey' => 'test_payment_key',
            'orderId' => 'test_order_123',
            'amount' => 10000,
        ]);

        $result = TossPayments::confirm($data);

        expect($result->paymentKey)->toBe('test_payment_key')
            ->and($result->orderId)->toBe('test_order_123')
            ->and($result->status)->toBe(PaymentStatus::DONE)
            ->and($result->totalAmount)->toBe(10000)
            ->and($result->isSuccessful())->toBeTrue();
    });

    it('결제 승인 실패 - 잘못된 금액', function () {
        Http::fake([
            'https://api.tosspayments.com/v2/payments/confirm' => Http::response([
                'code' => 'INVALID_PAYMENT_AMOUNT',
                'message' => '결제 금액이 일치하지 않습니다.',
            ], 400),
        ]);

        $data = PaymentConfirmData::fromArray([
            'paymentKey' => 'test_payment_key',
            'orderId' => 'test_order_123',
            'amount' => 99999,
        ]);

        TossPayments::confirm($data);
    })->throws(TossValidationException::class);

    it('결제 조회 성공', function () {
        Http::fake([
            'https://api.tosspayments.com/v2/payments/test_payment_key' => Http::response([
                'paymentKey' => 'test_payment_key',
                'orderId' => 'test_order_123',
                'orderName' => '테스트 상품',
                'status' => 'DONE',
                'totalAmount' => 10000,
                'balanceAmount' => 10000,
                'method' => '카드',
            ], 200),
        ]);

        $result = TossPayments::getPayment('test_payment_key');

        expect($result->paymentKey)->toBe('test_payment_key')
            ->and($result->status)->toBe(PaymentStatus::DONE);
    });
});

describe('PaymentConfirmData DTO 테스트', function () {
    it('배열로부터 DTO 생성', function () {
        $data = PaymentConfirmData::fromArray([
            'paymentKey' => 'pk_123',
            'orderId' => 'order_456',
            'amount' => 5000,
        ]);

        expect($data->paymentKey)->toBe('pk_123')
            ->and($data->orderId)->toBe('order_456')
            ->and($data->amount)->toBe(5000);
    });

    it('문자열 금액을 정수로 변환', function () {
        $data = PaymentConfirmData::fromArray([
            'paymentKey' => 'pk_123',
            'orderId' => 'order_456',
            'amount' => '15000',
        ]);

        expect($data->amount)->toBe(15000)
            ->and($data->amount)->toBeInt();
    });

    it('toArray 변환', function () {
        $data = new PaymentConfirmData(
            paymentKey: 'pk_test',
            orderId: 'ord_test',
            amount: 20000,
        );

        $array = $data->toArray();

        expect($array)->toBe([
            'paymentKey' => 'pk_test',
            'orderId' => 'ord_test',
            'amount' => 20000,
        ]);
    });
});

describe('PaymentStatus Enum 테스트', function () {
    it('결제 완료 상태 확인', function () {
        expect(PaymentStatus::DONE->isCompleted())->toBeTrue()
            ->and(PaymentStatus::READY->isCompleted())->toBeFalse();
    });

    it('결제 취소 상태 확인', function () {
        expect(PaymentStatus::CANCELED->isCanceled())->toBeTrue()
            ->and(PaymentStatus::PARTIAL_CANCELED->isCanceled())->toBeTrue()
            ->and(PaymentStatus::DONE->isCanceled())->toBeFalse();
    });

    it('결제 실패 상태 확인', function () {
        expect(PaymentStatus::ABORTED->isFailed())->toBeTrue()
            ->and(PaymentStatus::EXPIRED->isFailed())->toBeTrue()
            ->and(PaymentStatus::DONE->isFailed())->toBeFalse();
    });

    it('결제 대기 상태 확인', function () {
        expect(PaymentStatus::READY->isPending())->toBeTrue()
            ->and(PaymentStatus::IN_PROGRESS->isPending())->toBeTrue()
            ->and(PaymentStatus::WAITING_FOR_DEPOSIT->isPending())->toBeTrue()
            ->and(PaymentStatus::DONE->isPending())->toBeFalse();
    });
});

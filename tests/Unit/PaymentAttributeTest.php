<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use Seandoesdev\TossPayments\Attributes\Payment;
use Seandoesdev\TossPayments\Facades\TossPayments;

describe('Payment Attribute 테스트', function () {
    beforeEach(function () {
        Http::preventStrayRequests();
    });

    it('결제 승인 요청을 보낼 수 있다', function () {
        Http::fake([
            '*/payments/confirm' => Http::response([
                'paymentKey' => 'test_payment_key',
                'orderId' => 'test_order_id',
                'orderName' => '테스트 상품',
                'status' => 'DONE',
                'totalAmount' => 10000,
                'balanceAmount' => 10000,
                'method' => '카드',
            ], 200),
        ]);

        $response = TossPayments::for(Payment::class)
            ->paymentKey('test_payment_key')
            ->orderId('test_order_id')
            ->amount(10000)
            ->confirm();

        expect($response->successful())->toBeTrue();
        expect($response->json('status'))->toBe('DONE');
    });

    it('결제 조회 요청을 보낼 수 있다', function () {
        Http::fake([
            '*/payments/test_payment_key' => Http::response([
                'paymentKey' => 'test_payment_key',
                'orderId' => 'test_order_id',
                'status' => 'DONE',
                'totalAmount' => 10000,
            ], 200),
        ]);

        $response = TossPayments::for(Payment::class)
            ->paymentKey('test_payment_key')
            ->get();

        expect($response->successful())->toBeTrue();
        expect($response->json('paymentKey'))->toBe('test_payment_key');
    });

    it('주문 ID로 결제 조회를 할 수 있다', function () {
        Http::fake([
            '*/payments/orders/test_order_id' => Http::response([
                'paymentKey' => 'test_payment_key',
                'orderId' => 'test_order_id',
                'status' => 'DONE',
            ], 200),
        ]);

        $response = TossPayments::for(Payment::class)
            ->orderId('test_order_id')
            ->get();

        expect($response->successful())->toBeTrue();
        expect($response->json('orderId'))->toBe('test_order_id');
    });

    it('결제 취소 요청을 보낼 수 있다', function () {
        Http::fake([
            '*/payments/test_payment_key/cancel' => Http::response([
                'paymentKey' => 'test_payment_key',
                'status' => 'CANCELED',
                'cancels' => [
                    ['cancelAmount' => 10000, 'cancelReason' => '고객 요청'],
                ],
            ], 200),
        ]);

        $response = TossPayments::for(Payment::class)
            ->paymentKey('test_payment_key')
            ->cancelReason('고객 요청')
            ->cancel();

        expect($response->successful())->toBeTrue();
        expect($response->json('status'))->toBe('CANCELED');
    });

    it('부분 취소 요청을 보낼 수 있다', function () {
        Http::fake([
            '*/payments/test_payment_key/cancel' => Http::response([
                'paymentKey' => 'test_payment_key',
                'status' => 'PARTIAL_CANCELED',
                'cancels' => [
                    ['cancelAmount' => 5000, 'cancelReason' => '부분 취소'],
                ],
            ], 200),
        ]);

        $response = TossPayments::for(Payment::class)
            ->paymentKey('test_payment_key')
            ->cancelReason('부분 취소')
            ->cancel(cancelAmount: 5000);

        expect($response->successful())->toBeTrue();
        expect($response->json('status'))->toBe('PARTIAL_CANCELED');
    });

    it('가상계좌 발급 요청을 보낼 수 있다', function () {
        Http::fake([
            '*/virtual-accounts' => Http::response([
                'paymentKey' => 'test_payment_key',
                'orderId' => 'test_order_id',
                'virtualAccount' => [
                    'accountNumber' => '1234567890',
                    'bank' => '신한',
                ],
                'status' => 'WAITING_FOR_DEPOSIT',
            ], 200),
        ]);

        $response = TossPayments::for(Payment::class)
            ->amount(10000)
            ->orderId('test_order_id')
            ->orderName('테스트 상품')
            ->customerName('홍길동')
            ->bank('신한')
            ->virtualAccounts();

        expect($response->successful())->toBeTrue();
        expect($response->json('status'))->toBe('WAITING_FOR_DEPOSIT');
    });
});

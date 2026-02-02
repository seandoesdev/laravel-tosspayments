<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use Seandoesdev\TossPayments\Attributes\Transaction;
use Seandoesdev\TossPayments\Facades\TossPayments;

describe('Transaction Attribute 테스트', function () {
    beforeEach(function () {
        Http::preventStrayRequests();
    });

    it('거래 내역을 조회할 수 있다', function () {
        Http::fake([
            '*/transactions*' => Http::response([
                'hasMore' => false,
                'lastCursor' => null,
                'data' => [
                    [
                        'mId' => 'test_mid',
                        'transactionKey' => 'test_transaction_key',
                        'paymentKey' => 'test_payment_key',
                        'orderId' => 'test_order_id',
                        'amount' => 10000,
                    ],
                ],
            ], 200),
        ]);

        $response = TossPayments::for(Transaction::class)
            ->startDate('2024-01-01T00:00:00')
            ->endDate('2024-01-31T23:59:59')
            ->get();

        expect($response->successful())->toBeTrue();
        expect($response->json('hasMore'))->toBeFalse();
    });

    it('페이지네이션으로 거래 내역을 조회할 수 있다', function () {
        Http::fake([
            '*/transactions*' => Http::response([
                'hasMore' => true,
                'lastCursor' => 'cursor_123',
                'data' => [],
            ], 200),
        ]);

        $response = TossPayments::for(Transaction::class)
            ->startDate('2024-01-01T00:00:00')
            ->endDate('2024-01-31T23:59:59')
            ->startingAfter('cursor_abc')
            ->limit(100)
            ->get();

        expect($response->successful())->toBeTrue();
        expect($response->json('hasMore'))->toBeTrue();
    });
});

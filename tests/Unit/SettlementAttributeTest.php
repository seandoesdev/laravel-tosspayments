<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use Seandoesdev\TossPayments\Attributes\Settlement;
use Seandoesdev\TossPayments\Facades\TossPayments;

describe('Settlement Attribute 테스트', function () {
    beforeEach(function () {
        Http::preventStrayRequests();
    });

    it('정산 내역을 조회할 수 있다', function () {
        Http::fake([
            '*/settlements*' => Http::response([
                'hasMore' => false,
                'data' => [
                    [
                        'paymentKey' => 'test_payment_key',
                        'soldDate' => '2024-01-15',
                        'paidOutDate' => '2024-01-22',
                        'amount' => 10000,
                    ],
                ],
            ], 200),
        ]);

        $response = TossPayments::for(Settlement::class)
            ->startDate('2024-01-01')
            ->endDate('2024-01-31')
            ->get();

        expect($response->successful())->toBeTrue();
    });

    it('날짜 타입을 지정하여 정산 내역을 조회할 수 있다', function () {
        Http::fake([
            '*/settlements*' => Http::response([
                'hasMore' => false,
                'data' => [],
            ], 200),
        ]);

        $response = TossPayments::for(Settlement::class)
            ->startDate('2024-01-01')
            ->endDate('2024-01-31')
            ->dateType('paidOutDate')
            ->page(0)
            ->size(100)
            ->get();

        expect($response->successful())->toBeTrue();
    });

    it('수동 정산을 요청할 수 있다', function () {
        Http::fake([
            '*/settlements' => Http::response([
                'paymentKey' => 'test_payment_key',
                'status' => 'PENDING',
            ], 200),
        ]);

        $response = TossPayments::for(Settlement::class)
            ->paymentKey('test_payment_key')
            ->request();

        expect($response->successful())->toBeTrue();
    });
});

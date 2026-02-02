<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use Seandoesdev\TossPayments\Attributes\CashReceipt;
use Seandoesdev\TossPayments\Facades\TossPayments;

describe('CashReceipt Attribute 테스트', function () {
    beforeEach(function () {
        Http::preventStrayRequests();
    });

    it('현금영수증을 발급할 수 있다', function () {
        Http::fake([
            '*/cash-receipts' => Http::response([
                'receiptKey' => 'test_receipt_key',
                'orderId' => 'test_order_id',
                'orderName' => '테스트 상품',
                'type' => '소득공제',
                'issueStatus' => 'COMPLETED',
                'amount' => 10000,
            ], 200),
        ]);

        $response = TossPayments::for(CashReceipt::class)
            ->amount(10000)
            ->orderId('test_order_id')
            ->orderName('테스트 상품')
            ->customerIdentityNumber('01012345678')
            ->type('소득공제')
            ->issue();

        expect($response->successful())->toBeTrue();
        expect($response->json('receiptKey'))->toBe('test_receipt_key');
        expect($response->json('type'))->toBe('소득공제');
    });

    it('현금영수증을 취소할 수 있다', function () {
        Http::fake([
            '*/cash-receipts/test_receipt_key/cancel' => Http::response([
                'receiptKey' => 'test_receipt_key',
                'issueStatus' => 'CANCELED',
            ], 200),
        ]);

        $response = TossPayments::for(CashReceipt::class)
            ->receiptKey('test_receipt_key')
            ->cancel();

        expect($response->successful())->toBeTrue();
        expect($response->json('issueStatus'))->toBe('CANCELED');
    });

    it('requestId로 현금영수증을 조회할 수 있다', function () {
        Http::fake([
            '*/cash-receipts*' => Http::response([
                'receiptKey' => 'test_receipt_key',
                'orderId' => 'test_order_id',
            ], 200),
        ]);

        $response = TossPayments::for(CashReceipt::class)
            ->requestId('test_request_id')
            ->get();

        expect($response->successful())->toBeTrue();
    });
});

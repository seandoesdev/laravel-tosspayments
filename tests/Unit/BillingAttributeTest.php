<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use Seandoesdev\TossPayments\Attributes\Billing;
use Seandoesdev\TossPayments\Facades\TossPayments;

describe('Billing Attribute 테스트', function () {
    beforeEach(function () {
        Http::preventStrayRequests();
    });

    it('카드 정보로 빌링키를 발급할 수 있다', function () {
        Http::fake([
            '*/billing/authorizations/card' => Http::response([
                'billingKey' => 'test_billing_key',
                'customerKey' => 'test_customer_key',
                'cardCompany' => '신한',
                'cardNumber' => '123456******1234',
            ], 200),
        ]);

        $response = TossPayments::for(Billing::class)
            ->customerKey('test_customer_key')
            ->cardNumber('1234567890123456')
            ->cardExpirationYear('25')
            ->cardExpirationMonth('12')
            ->customerIdentityNumber('800101')
            ->authorizationsCard();

        expect($response->successful())->toBeTrue();
        expect($response->json('billingKey'))->toBe('test_billing_key');
    });

    it('결제창 인증으로 빌링키를 발급할 수 있다', function () {
        Http::fake([
            '*/billing/authorizations/issue' => Http::response([
                'billingKey' => 'test_billing_key',
                'customerKey' => 'test_customer_key',
            ], 200),
        ]);

        $response = TossPayments::for(Billing::class)
            ->authKey('test_auth_key')
            ->customerKey('test_customer_key')
            ->authorizationsIssue();

        expect($response->successful())->toBeTrue();
        expect($response->json('billingKey'))->toBe('test_billing_key');
    });

    it('빌링키로 자동 결제를 요청할 수 있다', function () {
        Http::fake([
            '*/billing/test_billing_key' => Http::response([
                'paymentKey' => 'test_payment_key',
                'orderId' => 'test_order_id',
                'status' => 'DONE',
                'totalAmount' => 9900,
            ], 200),
        ]);

        $response = TossPayments::for(Billing::class)
            ->billingKey('test_billing_key')
            ->customerKey('test_customer_key')
            ->amount(9900)
            ->orderId('test_order_id')
            ->orderName('월간 구독')
            ->request();

        expect($response->successful())->toBeTrue();
        expect($response->json('status'))->toBe('DONE');
        expect($response->json('totalAmount'))->toBe(9900);
    });
});

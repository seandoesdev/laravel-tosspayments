<?php

declare(strict_types=1);

use Seandoesdev\TossPayments\Exceptions\TossValidationException;
use Seandoesdev\TossPayments\Support\WebhookVerifier;

describe('웹훅 서명 검증 테스트', function () {
    it('유효한 서명 검증 성공', function () {
        $secretKey = 'test_webhook_secret';
        $verifier = new WebhookVerifier($secretKey, tolerance: 300);

        $payload = '{"eventType":"PAYMENT_STATUS_CHANGED"}';
        $timestamp = time();
        $signature = hash_hmac('sha256', "{$timestamp}.{$payload}", $secretKey);
        $signatureHeader = "t={$timestamp},v1={$signature}";

        $result = $verifier->verify($payload, $signatureHeader, $timestamp);

        expect($result)->toBeTrue();
    });

    it('잘못된 서명 형식 예외 발생', function () {
        $verifier = new WebhookVerifier('secret');

        $verifier->verify('payload', 'invalid_format');
    })->throws(TossValidationException::class, 'Invalid signature format');

    it('만료된 서명 예외 발생', function () {
        $secretKey = 'test_secret';
        $verifier = new WebhookVerifier($secretKey, tolerance: 300);

        $payload = '{"test": "data"}';
        $oldTimestamp = time() - 400; // 5분 초과
        $signature = hash_hmac('sha256', "{$oldTimestamp}.{$payload}", $secretKey);
        $signatureHeader = "t={$oldTimestamp},v1={$signature}";

        $verifier->verify($payload, $signatureHeader);
    })->throws(TossValidationException::class, 'Webhook signature has expired');

    it('잘못된 서명 값 예외 발생', function () {
        $verifier = new WebhookVerifier('real_secret', tolerance: 300);

        $timestamp = time();
        $wrongSignature = hash_hmac('sha256', "{$timestamp}.payload", 'wrong_secret');
        $signatureHeader = "t={$timestamp},v1={$wrongSignature}";

        $verifier->verify('payload', $signatureHeader, $timestamp);
    })->throws(TossValidationException::class, 'Webhook signature verification failed');

    it('타임스탬프 누락시 예외 발생', function () {
        $verifier = new WebhookVerifier('secret');

        $verifier->verify('payload', 'v1=abc123');
    })->throws(TossValidationException::class, 'Invalid signature format');

    it('서명 값 누락시 예외 발생', function () {
        $verifier = new WebhookVerifier('secret');

        $verifier->verify('payload', 't=123456789');
    })->throws(TossValidationException::class, 'Invalid signature format');
});

describe('WebhookVerifier 설정 테스트', function () {
    it('기본 tolerance는 300초', function () {
        $verifier = new WebhookVerifier('secret');

        // 299초 전 타임스탬프는 유효
        $payload = 'test';
        $timestamp = time() - 299;
        $signature = hash_hmac('sha256', "{$timestamp}.{$payload}", 'secret');
        $signatureHeader = "t={$timestamp},v1={$signature}";

        $result = $verifier->verify($payload, $signatureHeader);

        expect($result)->toBeTrue();
    });

    it('커스텀 tolerance 설정', function () {
        $verifier = new WebhookVerifier('secret', tolerance: 60);

        $payload = 'test';
        $timestamp = time() - 61; // 61초 전
        $signature = hash_hmac('sha256', "{$timestamp}.{$payload}", 'secret');
        $signatureHeader = "t={$timestamp},v1={$signature}";

        $verifier->verify($payload, $signatureHeader);
    })->throws(TossValidationException::class, 'Webhook signature has expired');
});

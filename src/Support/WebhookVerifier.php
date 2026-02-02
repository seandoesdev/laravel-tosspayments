<?php

declare(strict_types=1);

namespace Seandoesdev\TossPayments\Support;

use Seandoesdev\TossPayments\Exceptions\TossValidationException;

/**
 * Toss 웹훅 서명 검증기
 *
 * @see https://docs.tosspayments.com/reference/webhook
 */
class WebhookVerifier
{
    public function __construct(
        private readonly string $secretKey,
        private readonly int $tolerance = 300, // 5 minutes
    ) {
    }

    /**
     * 웹훅 서명 검증
     *
     * @param string $payload 요청 바디 (JSON 문자열)
     * @param string $signature Toss-Signature 헤더 값
     * @param int|null $timestamp 타임스탬프 (테스트용)
     *
     * @throws TossValidationException 서명 검증 실패 시
     */
    public function verify(string $payload, string $signature, ?int $timestamp = null): bool
    {
        $timestamp ??= time();

        // 서명 파싱 (t=timestamp,v1=signature 형식)
        $signatureParts = $this->parseSignature($signature);

        if ($signatureParts === null) {
            throw new TossValidationException(
                errorCode: 'INVALID_SIGNATURE_FORMAT',
                message: 'Invalid signature format',
                httpStatusCode: 401,
            );
        }

        // 타임스탬프 검증
        if ($this->isExpired($signatureParts['timestamp'], $timestamp)) {
            throw new TossValidationException(
                errorCode: 'SIGNATURE_EXPIRED',
                message: 'Webhook signature has expired',
                httpStatusCode: 401,
            );
        }

        // 서명 검증
        $expectedSignature = $this->computeSignature(
            $signatureParts['timestamp'],
            $payload,
        );

        if (!hash_equals($expectedSignature, $signatureParts['signature'])) {
            throw new TossValidationException(
                errorCode: 'INVALID_SIGNATURE',
                message: 'Webhook signature verification failed',
                httpStatusCode: 401,
            );
        }

        return true;
    }

    /**
     * 서명 계산
     */
    private function computeSignature(int $timestamp, string $payload): string
    {
        $signedPayload = "{$timestamp}.{$payload}";

        return hash_hmac('sha256', $signedPayload, $this->secretKey);
    }

    /**
     * 서명 문자열 파싱
     *
     * @return array{timestamp: int, signature: string}|null
     */
    private function parseSignature(string $signature): ?array
    {
        // 형식: t=1234567890,v1=abc123...
        $parts = [];

        foreach (explode(',', $signature) as $part) {
            $keyValue = explode('=', $part, 2);

            if (count($keyValue) !== 2) {
                return null;
            }

            $parts[$keyValue[0]] = $keyValue[1];
        }

        if (!isset($parts['t'], $parts['v1'])) {
            return null;
        }

        return [
            'timestamp' => (int) $parts['t'],
            'signature' => $parts['v1'],
        ];
    }

    /**
     * 타임스탬프 만료 확인
     */
    private function isExpired(int $signatureTimestamp, int $currentTimestamp): bool
    {
        return abs($currentTimestamp - $signatureTimestamp) > $this->tolerance;
    }
}

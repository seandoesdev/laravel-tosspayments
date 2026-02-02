<?php

declare(strict_types=1);

namespace Seandoesdev\TossPayments\Contracts;

/**
 * Toss Payments API 클라이언트 인터페이스
 */
interface PaymentClientInterface
{
    /**
     * API 요청 실행
     *
     * @param string $method HTTP 메서드 (GET, POST, etc.)
     * @param string $endpoint API 엔드포인트 경로
     * @param array<string, mixed> $data 요청 바디 데이터
     * @return array<string, mixed> 응답 데이터
     *
     * @throws \Seandoesdev\TossPayments\Exceptions\TossValidationException
     * @throws \Seandoesdev\TossPayments\Exceptions\TossServerException
     */
    public function request(string $method, string $endpoint, array $data = []): array;
}

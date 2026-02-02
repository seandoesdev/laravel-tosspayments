<?php

declare(strict_types=1);

namespace Seandoesdev\TossPayments\Exceptions;

use Exception;

/**
 * Toss API 유효성 검증 예외 (4xx 에러)
 */
class TossValidationException extends Exception
{
    public function __construct(
        public readonly string $errorCode,
        string $message,
        public readonly ?array $errors = null,
        public readonly ?array $rawResponse = null,
        int $httpStatusCode = 400,
    ) {
        parent::__construct($message, $httpStatusCode);
    }

    /**
     * API 응답으로부터 예외 생성
     *
     * @param array<string, mixed> $response
     */
    public static function fromResponse(array $response, int $statusCode = 400): self
    {
        return new self(
            errorCode: $response['code'] ?? 'UNKNOWN_ERROR',
            message: $response['message'] ?? 'Validation failed',
            errors: $response['errors'] ?? null,
            rawResponse: $response,
            httpStatusCode: $statusCode,
        );
    }

    /**
     * 에러 코드 반환
     */
    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    /**
     * 원본 응답 반환
     *
     * @return array<string, mixed>|null
     */
    public function getRawResponse(): ?array
    {
        return $this->rawResponse;
    }
}

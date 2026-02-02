<?php

declare(strict_types=1);

namespace Seandoesdev\TossPayments\Exceptions;

use Exception;

/**
 * Toss API 서버 예외 (5xx 에러)
 */
class TossServerException extends Exception
{
    public function __construct(
        string $message,
        public readonly ?array $rawResponse = null,
        int $httpStatusCode = 500,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $httpStatusCode, $previous);
    }

    /**
     * API 응답으로부터 예외 생성
     *
     * @param array<string, mixed> $response
     */
    public static function fromResponse(array $response, int $statusCode = 500): self
    {
        return new self(
            message: $response['message'] ?? 'Internal server error',
            rawResponse: $response,
            httpStatusCode: $statusCode,
        );
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

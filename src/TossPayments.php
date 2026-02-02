<?php

declare(strict_types=1);

namespace Seandoesdev\TossPayments;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Seandoesdev\TossPayments\Exceptions\TossServerException;
use Seandoesdev\TossPayments\Exceptions\TossValidationException;

/**
 * Toss Payments API 클라이언트
 *
 * @see https://docs.tosspayments.com/reference
 */
class TossPayments
{
    protected mixed $attribute;

    protected PendingRequest $client;

    protected string $endpoint;

    protected string $version;

    protected string $url;

    protected string $clientKey;

    protected string $secretKey;

    protected array $headers = [];

    public function __construct(mixed $attribute = null)
    {
        $this->setAttribute($attribute)
            ->initializeApiUrl()
            ->initializeKeys()
            ->initializeHeaders()
            ->initializeHttp();
    }

    /**
     * Attribute 설정
     */
    public function setAttribute(mixed $attribute): static
    {
        $this->attribute = $attribute;

        return $this;
    }

    /**
     * 정적 팩토리 메서드
     *
     * @template T
     * @param class-string<T> $attribute
     * @return T
     */
    public static function for(string $attribute): mixed
    {
        return new $attribute();
    }

    /**
     * Attribute로 메서드 호출 위임
     */
    public function __call(string $name, array $arguments): mixed
    {
        return $this->attribute->{$name}(...$arguments);
    }

    /**
     * API URL 초기화
     */
    protected function initializeApiUrl(): static
    {
        $this->endpoint = config('tosspayments.base_url', 'https://api.tosspayments.com');
        $this->version = config('tosspayments.version', 'v1');
        $this->url = $this->endpoint . '/' . $this->version;

        return $this;
    }

    /**
     * API 키 초기화
     */
    protected function initializeKeys(): static
    {
        $this->clientKey = config('tosspayments.client_key', '');
        $this->secretKey = config('tosspayments.secret_key', '');

        return $this;
    }

    /**
     * HTTP 헤더 초기화
     */
    protected function initializeHeaders(): static
    {
        $this->headers = [
            'Authorization' => 'Basic ' . base64_encode($this->secretKey . ':'),
            'Content-Type' => 'application/json',
        ];

        return $this;
    }

    /**
     * HTTP 클라이언트 초기화
     */
    protected function initializeHttp(): static
    {
        $timeout = config('tosspayments.timeout', 30);
        $retryTimes = config('tosspayments.retry.times', 3);
        $retrySleep = config('tosspayments.retry.sleep', 100);

        $headers = $this->headers;

        // v2 API는 Idempotency-Key 헤더 필수
        if ($this->version === 'v2') {
            $headers['Idempotency-Key'] = Str::uuid()->toString();
        }

        $this->client = Http::withHeaders($headers)
            ->timeout($timeout)
            ->retry($retryTimes, $retrySleep, function ($exception) {
                return $exception instanceof \Illuminate\Http\Client\RequestException
                    && $exception->response?->status() >= 500;
            })
            ->acceptJson();

        return $this;
    }

    /**
     * 문자열에 접두사 추가
     */
    public function start(?string $value, string $prefix = '/'): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        return Str::start($value, $prefix);
    }

    /**
     * 테스트 코드 헤더 설정
     */
    public function testCode(string $code): static
    {
        $headers = $this->headers + ['TossPayments-Test-Code' => $code];

        // v2 API는 Idempotency-Key 헤더 필수
        if ($this->version === 'v2') {
            $headers['Idempotency-Key'] = Str::uuid()->toString();
        }

        $this->client = Http::withHeaders($headers)
            ->timeout(config('tosspayments.timeout', 30))
            ->acceptJson();

        return $this;
    }

    /**
     * HTTP 클라이언트 반환
     */
    public function getClient(): PendingRequest
    {
        return $this->client;
    }

    /**
     * API URL 반환
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * API 응답 처리
     *
     * @throws TossValidationException
     * @throws TossServerException
     */
    public function handleResponse(Response $response): array
    {
        if ($response->successful()) {
            return $response->json() ?? [];
        }

        $json = $response->json() ?? [];
        $statusCode = $response->status();

        if ($statusCode >= 400 && $statusCode < 500) {
            throw TossValidationException::fromResponse($json, $statusCode);
        }

        throw TossServerException::fromResponse($json, $statusCode);
    }
}

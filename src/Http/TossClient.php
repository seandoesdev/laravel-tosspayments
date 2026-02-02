<?php

declare(strict_types=1);

namespace Seandoesdev\TossPayments\Http;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Seandoesdev\TossPayments\Contracts\PaymentClientInterface;
use Seandoesdev\TossPayments\Exceptions\TossServerException;
use Seandoesdev\TossPayments\Exceptions\TossValidationException;

/**
 * Toss Payments API HTTP 클라이언트
 */
class TossClient implements PaymentClientInterface
{
    private string $baseUrl;
    private string $version;
    private int $timeout;
    private array $retryConfig;
    private bool $sslVerify;

    public function __construct(
        private readonly string $secretKey,
        ?string $baseUrl = null,
        ?string $version = null,
        ?int $timeout = null,
        ?array $retryConfig = null,
        ?bool $sslVerify = null,
    ) {
        $this->baseUrl = $baseUrl ?? config('tosspayments.base_url', 'https://api.tosspayments.com');
        $this->version = $version ?? config('tosspayments.version', 'v2');
        $this->timeout = $timeout ?? config('tosspayments.timeout', 30);
        $this->retryConfig = $retryConfig ?? config('tosspayments.retry', [
            'times' => 3,
            'sleep' => 100,
        ]);
        $this->sslVerify = $sslVerify ?? config('tosspayments.ssl_verify', true);
    }

    /**
     * API 요청 실행
     *
     * @param string $method HTTP 메서드 (GET, POST, etc.)
     * @param string $endpoint API 엔드포인트 경로
     * @param array<string, mixed> $data 요청 바디 데이터
     * @return array<string, mixed> 응답 데이터
     *
     * @throws TossValidationException
     * @throws TossServerException
     */
    public function request(string $method, string $endpoint, array $data = []): array
    {
        $url = $this->buildUrl($endpoint);

        try {
            $response = $this->buildRequest()
                ->$method($url, $data);

            if ($response->failed()) {
                $this->handleErrorResponse($response->json() ?? [], $response->status());
            }

            return $response->json() ?? [];
        } catch (RequestException $e) {
            if ($e->response) {
                $this->handleErrorResponse($e->response->json() ?? [], $e->response->status());
            }

            throw new TossServerException(
                message: 'API request failed: ' . $e->getMessage(),
                rawResponse: $e->response?->json(),
                httpStatusCode: $e->response?->status() ?? 500,
                previous: $e,
            );
        }
    }

    /**
     * HTTP 클라이언트 빌더
     */
    private function buildRequest(): PendingRequest
    {
        return Http::withBasicAuth($this->secretKey, '')
            ->timeout($this->timeout)
            ->withOptions(['verify' => $this->sslVerify])
            ->retry(
                $this->retryConfig['times'],
                $this->retryConfig['sleep'],
                fn($exception) => $exception instanceof RequestException
                && $exception->response?->status() >= 500,
            )
            ->acceptJson()
            ->asJson();
    }

    /**
     * 전체 URL 빌드
     */
    private function buildUrl(string $endpoint): string
    {
        $endpoint = ltrim($endpoint, '/');

        return sprintf('%s/%s/%s', $this->baseUrl, $this->version, $endpoint);
    }

    /**
     * 에러 응답 처리
     *
     * @param array<string, mixed> $response
     * @throws TossValidationException
     * @throws TossServerException
     */
    private function handleErrorResponse(array $response, int $statusCode): never
    {
        if ($statusCode >= 400 && $statusCode < 500) {
            throw TossValidationException::fromResponse($response, $statusCode);
        }

        throw TossServerException::fromResponse($response, $statusCode);
    }
}

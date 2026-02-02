<?php

declare(strict_types=1);

namespace Seandoesdev\TossPayments\Attributes;

use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\Response;
use Seandoesdev\TossPayments\Contracts\AttributeInterface;
use Seandoesdev\TossPayments\TossPayments;

/**
 * 거래 조회 API
 *
 * @see https://docs.tosspayments.com/reference#%EA%B1%B0%EB%9E%98-%EC%A1%B0%ED%9A%8C
 */
class Transaction extends TossPayments implements AttributeInterface
{
    protected string $uri;

    protected string $startDate;

    protected string $endDate;

    protected ?string $startingAfter = null;

    protected ?int $limit = null;

    public function __construct()
    {
        parent::__construct($this);
        $this->initializeUri();
    }

    /**
     * URI 초기화
     */
    public function initializeUri(): static
    {
        $this->uri = '/transactions';

        return $this;
    }

    /**
     * 엔드포인트 생성
     */
    public function createEndpoint(?string $endpoint, bool $withUri = true): string
    {
        if ($withUri) {
            return $this->url . $this->uri . $this->start($endpoint);
        }

        return $this->url . $this->start($endpoint);
    }

    /**
     * 조회 시작 날짜 설정 (ISO 8601 형식: yyyy-MM-dd'T'HH:mm:ss)
     */
    public function startDate(string $startDate): static
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * 조회 종료 날짜 설정 (ISO 8601 형식: yyyy-MM-dd'T'HH:mm:ss)
     */
    public function endDate(string $endDate): static
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * 페이지네이션 커서 설정
     */
    public function startingAfter(string $startingAfter): static
    {
        $this->startingAfter = $startingAfter;

        return $this;
    }

    /**
     * 조회 개수 제한 설정 (최대 10000)
     */
    public function limit(int $limit): static
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * 거래 내역 조회
     *
     * @see https://docs.tosspayments.com/reference#%EA%B1%B0%EB%9E%98-%EC%A1%B0%ED%9A%8C
     */
    public function get(): PromiseInterface|Response
    {
        $parameters = [
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
        ];

        if ($this->startingAfter !== null) {
            $parameters['startingAfter'] = $this->startingAfter;
        }

        if ($this->limit !== null) {
            $parameters['limit'] = $this->limit;
        }

        return $this->client->get($this->createEndpoint(''), $parameters);
    }
}

<?php

declare(strict_types=1);

namespace Seandoesdev\TossPayments\Attributes;

use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\Response;
use Seandoesdev\TossPayments\Contracts\AttributeInterface;
use Seandoesdev\TossPayments\TossPayments;

/**
 * 정산 API
 *
 * @see https://docs.tosspayments.com/reference#%EC%A0%95%EC%82%B0
 */
class Settlement extends TossPayments implements AttributeInterface
{
    protected string $uri;

    protected string $startDate;

    protected string $endDate;

    protected ?string $dateType = null;

    protected ?int $page = null;

    protected ?int $size = null;

    protected string $paymentKey;

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
        $this->uri = '/settlements';

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
     * 조회 시작 날짜 설정 (yyyy-MM-dd 형식)
     */
    public function startDate(string $startDate): static
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * 조회 종료 날짜 설정 (yyyy-MM-dd 형식)
     */
    public function endDate(string $endDate): static
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * 날짜 타입 설정
     *
     * @param string $dateType 'soldDate' (판매일), 'paidOutDate' (지급일)
     */
    public function dateType(string $dateType): static
    {
        $this->dateType = $dateType;

        return $this;
    }

    /**
     * 페이지 번호 설정 (0부터 시작)
     */
    public function page(int $page): static
    {
        $this->page = $page;

        return $this;
    }

    /**
     * 페이지 크기 설정 (기본값: 100, 최대: 10000)
     */
    public function size(int $size): static
    {
        $this->size = $size;

        return $this;
    }

    /**
     * 결제 키 설정
     */
    public function paymentKey(string $paymentKey): static
    {
        $this->paymentKey = $paymentKey;

        return $this;
    }

    /**
     * 정산 조회
     *
     * @see https://docs.tosspayments.com/reference#%EC%A0%95%EC%82%B0-%EC%A1%B0%ED%9A%8C
     */
    public function get(): PromiseInterface|Response
    {
        $parameters = [
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
        ];

        if ($this->dateType !== null) {
            $parameters['dateType'] = $this->dateType;
        }

        if ($this->page !== null) {
            $parameters['page'] = $this->page;
        }

        if ($this->size !== null) {
            $parameters['size'] = $this->size;
        }

        return $this->client->get($this->createEndpoint(''), $parameters);
    }

    /**
     * 수동 정산 요청
     *
     * @see https://docs.tosspayments.com/reference#%EC%88%98%EB%8F%99-%EC%A0%95%EC%82%B0-%EC%9A%94%EC%B2%AD
     */
    public function request(): PromiseInterface|Response
    {
        return $this->client->post($this->createEndpoint(''), [
            'paymentKey' => $this->paymentKey,
        ]);
    }
}

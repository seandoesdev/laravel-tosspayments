<?php

declare(strict_types=1);

namespace Seandoesdev\TossPayments\Attributes;

use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\Response;
use Seandoesdev\TossPayments\Contracts\AttributeInterface;
use Seandoesdev\TossPayments\TossPayments;

/**
 * 카드 프로모션 API
 *
 * @see https://docs.tosspayments.com/reference#%ED%94%84%EB%A1%9C%EB%AA%A8%EC%85%98
 */
class Promotion extends TossPayments implements AttributeInterface
{
    protected string $uri;

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
        $this->uri = '/promotions';

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
     * 카드사 혜택 조회
     *
     * @see https://docs.tosspayments.com/reference#%EC%B9%B4%EB%93%9C%EC%82%AC-%ED%98%9C%ED%83%9D-%EC%A1%B0%ED%9A%8C
     */
    public function get(): PromiseInterface|Response
    {
        return $this->client->get($this->createEndpoint('/card'));
    }
}

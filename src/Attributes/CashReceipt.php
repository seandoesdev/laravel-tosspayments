<?php

declare(strict_types=1);

namespace Seandoesdev\TossPayments\Attributes;

use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\Response;
use Seandoesdev\TossPayments\Contracts\AttributeInterface;
use Seandoesdev\TossPayments\TossPayments;

/**
 * 현금영수증 API
 *
 * @see https://docs.tosspayments.com/reference#%ED%98%84%EA%B8%88%EC%98%81%EC%88%98%EC%A6%9D
 */
class CashReceipt extends TossPayments implements AttributeInterface
{
    protected string $uri;

    protected int $amount;

    protected string $orderId;

    protected string $orderName;

    protected string $customerIdentityNumber;

    protected string $type;

    protected string $receiptKey;

    protected string $requestId;

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
        $this->uri = '/cash-receipts';

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
     * 금액 설정
     */
    public function amount(int $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * 주문 ID 설정
     */
    public function orderId(string $orderId): static
    {
        $this->orderId = $orderId;

        return $this;
    }

    /**
     * 주문명 설정
     */
    public function orderName(string $orderName): static
    {
        $this->orderName = $orderName;

        return $this;
    }

    /**
     * 고객 식별 번호 설정 (휴대폰 번호, 현금영수증 카드, 주민등록번호, 사업자등록번호)
     */
    public function customerIdentityNumber(string $customerIdentityNumber): static
    {
        $this->customerIdentityNumber = $customerIdentityNumber;

        return $this;
    }

    /**
     * 현금영수증 타입 설정 (소득공제, 지출증빙)
     *
     * @param string $type '소득공제' 또는 '지출증빙'
     */
    public function type(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    /**
     * 영수증 키 설정
     */
    public function receiptKey(string $receiptKey): static
    {
        $this->receiptKey = $receiptKey;

        return $this;
    }

    /**
     * 요청 ID 설정 (조회용)
     */
    public function requestId(string $requestId): static
    {
        $this->requestId = $requestId;

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
     * 현금영수증 발급
     *
     * @see https://docs.tosspayments.com/reference#%ED%98%84%EA%B8%88%EC%98%81%EC%88%98%EC%A6%9D-%EB%B0%9C%EA%B8%89
     */
    public function issue(
        ?int $taxFreeAmount = null,
    ): PromiseInterface|Response {
        $parameters = [
            'amount' => $this->amount,
            'orderId' => $this->orderId,
            'orderName' => $this->orderName,
            'customerIdentityNumber' => $this->customerIdentityNumber,
            'type' => $this->type,
        ];

        if ($taxFreeAmount !== null) {
            $parameters['taxFreeAmount'] = $taxFreeAmount;
        }

        return $this->client->post($this->createEndpoint(''), $parameters);
    }

    /**
     * 현금영수증 취소
     *
     * @see https://docs.tosspayments.com/reference#%ED%98%84%EA%B8%88%EC%98%81%EC%88%98%EC%A6%9D-%EC%B7%A8%EC%86%8C
     */
    public function cancel(?int $cancelAmount = null): PromiseInterface|Response
    {
        $parameters = [];

        if ($cancelAmount !== null) {
            $parameters['amount'] = $cancelAmount;
        }

        return $this->client->post($this->createEndpoint('/' . $this->receiptKey . '/cancel'), $parameters);
    }

    /**
     * 현금영수증 조회 (requestId로)
     *
     * @see https://docs.tosspayments.com/reference#%ED%98%84%EA%B8%88%EC%98%81%EC%88%98%EC%A6%9D-%EC%A1%B0%ED%9A%8C
     */
    public function get(): PromiseInterface|Response
    {
        return $this->client->get($this->createEndpoint(''), [
            'requestId' => $this->requestId,
        ]);
    }

    /**
     * 결제 건의 현금영수증 조회
     *
     * @see https://docs.tosspayments.com/reference#%EA%B2%B0%EC%A0%9C-%EA%B1%B4%EC%9D%98-%ED%98%84%EA%B8%88%EC%98%81%EC%88%98%EC%A6%9D-%EC%A1%B0%ED%9A%8C
     */
    public function getByPaymentKey(): PromiseInterface|Response
    {
        return $this->client->get($this->url . '/payments/' . $this->paymentKey . '/cash-receipt');
    }
}

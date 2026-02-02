<?php

declare(strict_types=1);

namespace Seandoesdev\TossPayments\Attributes;

use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\Response;
use Seandoesdev\TossPayments\Contracts\AttributeInterface;
use Seandoesdev\TossPayments\TossPayments;

/**
 * 빌링(자동결제) API
 *
 * @see https://docs.tosspayments.com/reference#%EB%B9%8C%EB%A7%81
 */
class Billing extends TossPayments implements AttributeInterface
{
    protected string $uri;

    protected string $customerKey;

    protected string $cardNumber;

    protected string $cardExpirationYear;

    protected string $cardExpirationMonth;

    protected string $customerIdentityNumber;

    protected string $authKey;

    protected string $billingKey;

    protected int $amount;

    protected string $orderName;

    protected string $orderId;

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
        $this->uri = '/billing';

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
     * 고객 키 설정
     */
    public function customerKey(string $customerKey): static
    {
        $this->customerKey = $customerKey;

        return $this;
    }

    /**
     * 카드 번호 설정
     */
    public function cardNumber(string $cardNumber): static
    {
        $this->cardNumber = $cardNumber;

        return $this;
    }

    /**
     * 카드 유효 연도 설정 (2자리)
     */
    public function cardExpirationYear(string $cardExpirationYear): static
    {
        $this->cardExpirationYear = $cardExpirationYear;

        return $this;
    }

    /**
     * 카드 유효 월 설정 (2자리)
     */
    public function cardExpirationMonth(string $cardExpirationMonth): static
    {
        $this->cardExpirationMonth = $cardExpirationMonth;

        return $this;
    }

    /**
     * 고객 식별 번호 설정 (생년월일 6자리 또는 사업자등록번호 10자리)
     */
    public function customerIdentityNumber(string $customerIdentityNumber): static
    {
        $this->customerIdentityNumber = $customerIdentityNumber;

        return $this;
    }

    /**
     * 인증 키 설정 (결제창 방식)
     */
    public function authKey(string $authKey): static
    {
        $this->authKey = $authKey;

        return $this;
    }

    /**
     * 카드 정보로 빌링키 발급
     *
     * @see https://docs.tosspayments.com/reference#%EC%B9%B4%EB%93%9C-%EC%9E%90%EB%8F%99%EA%B2%B0%EC%A0%9C-%EB%B9%8C%EB%A7%81%ED%82%A4-%EB%B0%9C%EA%B8%89
     */
    public function authorizationsCard(
        ?string $cardPassword = null,
        ?string $customerName = null,
        ?string $customerEmail = null,
        ?array $vbv = null,
    ): PromiseInterface|Response {
        $parameters = [
            'customerKey' => $this->customerKey,
            'cardNumber' => $this->cardNumber,
            'cardExpirationYear' => $this->cardExpirationYear,
            'cardExpirationMonth' => $this->cardExpirationMonth,
            'customerIdentityNumber' => $this->customerIdentityNumber,
        ];

        if ($cardPassword !== null) {
            $parameters['cardPassword'] = $cardPassword;
        }

        if ($customerName !== null) {
            $parameters['customerName'] = $customerName;
        }

        if ($customerEmail !== null) {
            $parameters['customerEmail'] = $customerEmail;
        }

        if ($vbv !== null) {
            $parameters['vbv'] = $vbv;
        }

        return $this->client->post($this->createEndpoint('/authorizations/card'), $parameters);
    }

    /**
     * 결제창 인증으로 빌링키 발급
     *
     * @see https://docs.tosspayments.com/reference#%EB%B9%8C%EB%A7%81%ED%82%A4-%EB%B0%9C%EA%B8%89
     */
    public function authorizationsIssue(): PromiseInterface|Response
    {
        return $this->client->post($this->createEndpoint('/authorizations/issue'), [
            'authKey' => $this->authKey,
            'customerKey' => $this->customerKey,
        ]);
    }

    /**
     * 빌링키 설정
     */
    public function billingKey(string $billingKey): static
    {
        $this->billingKey = $billingKey;

        return $this;
    }

    /**
     * 결제 금액 설정
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
     * 빌링키로 자동 결제 승인
     *
     * @see https://docs.tosspayments.com/reference#%EB%B9%8C%EB%A7%81%ED%82%A4%EB%A1%9C-%EA%B2%B0%EC%A0%9C-%EC%8A%B9%EC%9D%B8
     */
    public function request(
        ?string $customerEmail = null,
        ?string $customerName = null,
        ?string $customerMobilePhone = null,
        ?int $taxFreeAmount = null,
        ?int $cardInstallmentPlan = null,
    ): PromiseInterface|Response {
        $parameters = [
            'amount' => $this->amount,
            'customerKey' => $this->customerKey,
            'orderId' => $this->orderId,
            'orderName' => $this->orderName,
        ];

        if ($customerEmail !== null) {
            $parameters['customerEmail'] = $customerEmail;
        }

        if ($customerName !== null) {
            $parameters['customerName'] = $customerName;
        }

        if ($customerMobilePhone !== null) {
            $parameters['customerMobilePhone'] = $customerMobilePhone;
        }

        if ($taxFreeAmount !== null) {
            $parameters['taxFreeAmount'] = $taxFreeAmount;
        }

        if ($cardInstallmentPlan !== null) {
            $parameters['cardInstallmentPlan'] = $cardInstallmentPlan;
        }

        return $this->client->post($this->createEndpoint('/' . $this->billingKey), $parameters);
    }
}

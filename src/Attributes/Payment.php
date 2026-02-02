<?php

declare(strict_types=1);

namespace Seandoesdev\TossPayments\Attributes;

use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\Response;
use Seandoesdev\TossPayments\Contracts\AttributeInterface;
use Seandoesdev\TossPayments\TossPayments;

/**
 * 결제 API
 *
 * @see https://docs.tosspayments.com/reference#%EA%B2%B0%EC%A0%9C
 */
class Payment extends TossPayments implements AttributeInterface
{
    protected string $uri;

    protected string $paymentKey;

    protected string $orderId;

    protected string $cancelReason;

    protected string $orderName;

    protected string $customerName;

    protected string $bank;

    protected string $cardNumber;

    protected string $cardExpirationYear;

    protected string $cardExpirationMonth;

    protected string $customerIdentityNumber;

    protected int $amount;

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
        $this->uri = '/payments';

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
     * 결제 키 설정
     */
    public function paymentKey(string $paymentKey): static
    {
        $this->paymentKey = $paymentKey;

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
     * 결제 금액 설정
     */
    public function amount(int $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * 결제 승인
     *
     * @see https://docs.tosspayments.com/reference#%EA%B2%B0%EC%A0%9C-%EC%8A%B9%EC%9D%B8
     */
    public function confirm(): PromiseInterface|Response
    {
        return $this->client->post($this->createEndpoint('/confirm'), [
            'paymentKey' => $this->paymentKey,
            'orderId' => $this->orderId,
            'amount' => $this->amount,
        ]);
    }

    /**
     * 결제 조회 (paymentKey 또는 orderId)
     *
     * @see https://docs.tosspayments.com/reference#%EA%B2%B0%EC%A0%9C-%EC%A1%B0%ED%9A%8C
     */
    public function get(): PromiseInterface|Response
    {
        $endpoint = isset($this->paymentKey)
            ? '/' . $this->paymentKey
            : '/orders/' . $this->orderId;

        return $this->client->get($this->createEndpoint($endpoint));
    }

    /**
     * 취소 사유 설정
     */
    public function cancelReason(string $cancelReason): static
    {
        $this->cancelReason = $cancelReason;

        return $this;
    }

    /**
     * 결제 취소
     *
     * @see https://docs.tosspayments.com/reference#%EA%B2%B0%EC%A0%9C-%EC%B7%A8%EC%86%8C
     */
    public function cancel(
        ?int $cancelAmount = null,
        ?array $refundReceiveAccount = null,
        ?int $taxFreeAmount = null,
        ?int $refundableAmount = null,
    ): PromiseInterface|Response {
        $parameters = ['cancelReason' => $this->cancelReason];

        if ($cancelAmount !== null) {
            $parameters['cancelAmount'] = $cancelAmount;
        }

        if ($refundReceiveAccount !== null) {
            $parameters['refundReceiveAccount'] = $refundReceiveAccount;
        }

        if ($taxFreeAmount !== null) {
            $parameters['taxFreeAmount'] = $taxFreeAmount;
        }

        if ($refundableAmount !== null) {
            $parameters['refundableAmount'] = $refundableAmount;
        }

        return $this->client->post($this->createEndpoint('/' . $this->paymentKey . '/cancel'), $parameters);
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
     * 고객명 설정
     */
    public function customerName(string $customerName): static
    {
        $this->customerName = $customerName;

        return $this;
    }

    /**
     * 은행 코드 설정
     */
    public function bank(string $bank): static
    {
        $this->bank = $bank;

        return $this;
    }

    /**
     * 가상계좌 발급
     *
     * @see https://docs.tosspayments.com/reference#%EA%B0%80%EC%83%81%EA%B3%84%EC%A2%8C-%EB%B0%9C%EA%B8%89
     */
    public function virtualAccounts(
        ?string $accountType = null,
        ?string $accountKey = null,
        ?int $validHours = null,
        ?string $dueDate = null,
        ?string $customerEmail = null,
        ?string $customerMobilePhone = null,
        ?int $taxFreeAmount = null,
        ?bool $useEscrow = null,
        ?array $cashReceipt = null,
        ?array $escrowProducts = null,
    ): PromiseInterface|Response {
        $parameters = [
            'amount' => $this->amount,
            'orderId' => $this->orderId,
            'orderName' => $this->orderName,
            'customerName' => $this->customerName,
            'bank' => $this->bank,
        ];

        if ($accountType !== null) {
            $parameters['accountType'] = $accountType;
        }

        if ($accountKey !== null) {
            $parameters['accountKey'] = $accountKey;
        }

        if ($validHours !== null) {
            $parameters['validHours'] = $validHours;
        }

        if ($dueDate !== null) {
            $parameters['dueDate'] = $dueDate;
        }

        if ($customerEmail !== null) {
            $parameters['customerEmail'] = $customerEmail;
        }

        if ($customerMobilePhone !== null) {
            $parameters['customerMobilePhone'] = $customerMobilePhone;
        }

        if ($taxFreeAmount !== null) {
            $parameters['taxFreeAmount'] = $taxFreeAmount;
        }

        if ($useEscrow !== null) {
            $parameters['useEscrow'] = $useEscrow;
        }

        if ($cashReceipt !== null) {
            $parameters['cashReceipt'] = $cashReceipt;
        }

        if ($escrowProducts !== null) {
            $parameters['escrowProducts'] = $escrowProducts;
        }

        return $this->client->post($this->createEndpoint('/virtual-accounts', false), $parameters);
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
     * 카드 유효 연도 설정
     */
    public function cardExpirationYear(string $cardExpirationYear): static
    {
        $this->cardExpirationYear = $cardExpirationYear;

        return $this;
    }

    /**
     * 카드 유효 월 설정
     */
    public function cardExpirationMonth(string $cardExpirationMonth): static
    {
        $this->cardExpirationMonth = $cardExpirationMonth;

        return $this;
    }

    /**
     * 고객 식별 번호 설정 (생년월일 또는 사업자등록번호)
     */
    public function customerIdentityNumber(string $customerIdentityNumber): static
    {
        $this->customerIdentityNumber = $customerIdentityNumber;

        return $this;
    }

    /**
     * 카드 키인 결제
     *
     * @see https://docs.tosspayments.com/reference#%EC%B9%B4%EB%93%9C-%ED%82%A4%EC%9D%B8-%EA%B2%B0%EC%A0%9C
     */
    public function keyIn(
        ?string $cardPassword = null,
        ?int $cardInstallmentPlan = null,
        ?bool $useFreeInstallmentPlan = null,
        ?int $taxFreeAmount = null,
        ?string $customerEmail = null,
        ?string $customerName = null,
        ?array $vbv = null,
    ): PromiseInterface|Response {
        $parameters = [
            'amount' => $this->amount,
            'orderId' => $this->orderId,
            'orderName' => $this->orderName,
            'cardNumber' => $this->cardNumber,
            'cardExpirationYear' => $this->cardExpirationYear,
            'cardExpirationMonth' => $this->cardExpirationMonth,
            'customerIdentityNumber' => $this->customerIdentityNumber,
        ];

        if ($cardPassword !== null) {
            $parameters['cardPassword'] = $cardPassword;
        }

        if ($cardInstallmentPlan !== null) {
            $parameters['cardInstallmentPlan'] = $cardInstallmentPlan;
        }

        if ($useFreeInstallmentPlan !== null) {
            $parameters['useFreeInstallmentPlan'] = $useFreeInstallmentPlan;
        }

        if ($taxFreeAmount !== null) {
            $parameters['taxFreeAmount'] = $taxFreeAmount;
        }

        if ($customerEmail !== null) {
            $parameters['customerEmail'] = $customerEmail;
        }

        if ($customerName !== null) {
            $parameters['customerName'] = $customerName;
        }

        if ($vbv !== null) {
            $parameters['vbv'] = $vbv;
        }

        return $this->client->post($this->createEndpoint('/key-in'), $parameters);
    }
}

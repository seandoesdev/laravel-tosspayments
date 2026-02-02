<?php

declare(strict_types=1);

namespace Seandoesdev\TossPayments\Enums;

/**
 * 웹훅 이벤트 타입 Enum
 *
 * @see https://docs.tosspayments.com/reference/using-api/webhook-events
 */
enum WebhookEventType: string
{
    /**
     * 결제 상태 변경
     */
    case PAYMENT_STATUS_CHANGED = 'PAYMENT_STATUS_CHANGED';

    /**
     * 가상계좌 입금 완료
     */
    case DEPOSIT_CALLBACK = 'DEPOSIT_CALLBACK';

    /**
     * 지급대행 상태 변경
     */
    case PAYOUT_STATUS_CHANGED = 'PAYOUT_STATUS_CHANGED';

    /**
     * 브랜드페이 상태 변경
     */
    case BRAND_PAY_STATUS_CHANGED = 'BRAND_PAY_STATUS_CHANGED';

    /**
     * 결제 수단 업데이트
     */
    case METHOD_UPDATED = 'METHOD_UPDATED';

    /**
     * 빌링키 삭제
     */
    case BILLING_KEY_DELETED = 'BILLING_KEY_DELETED';

    /**
     * 결제 관련 이벤트인지 확인
     */
    public function isPaymentEvent(): bool
    {
        return in_array($this, [
            self::PAYMENT_STATUS_CHANGED,
            self::DEPOSIT_CALLBACK,
        ], true);
    }

    /**
     * 입금 관련 이벤트인지 확인
     */
    public function isDepositEvent(): bool
    {
        return $this === self::DEPOSIT_CALLBACK;
    }

    /**
     * 빌링 관련 이벤트인지 확인
     */
    public function isBillingEvent(): bool
    {
        return $this === self::BILLING_KEY_DELETED;
    }
}

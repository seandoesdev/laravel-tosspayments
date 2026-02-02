<?php

declare(strict_types=1);

namespace Seandoesdev\TossPayments\Enums;

/**
 * 결제 상태 Enum
 *
 * @see https://docs.tosspayments.com/reference#%EA%B2%B0%EC%A0%9C
 */
enum PaymentStatus: string
{
    /**
     * 결제 생성 후 결제 수단 선택 전 초기 상태
     */
    case READY = 'READY';

    /**
     * 결제 수단 인증 완료 상태 (결제 승인 전)
     */
    case IN_PROGRESS = 'IN_PROGRESS';

    /**
     * 가상계좌 발급 완료, 입금 대기 중 상태
     */
    case WAITING_FOR_DEPOSIT = 'WAITING_FOR_DEPOSIT';

    /**
     * 결제 승인 완료 상태
     */
    case DONE = 'DONE';

    /**
     * 결제 취소 상태
     */
    case CANCELED = 'CANCELED';

    /**
     * 결제 부분 취소 상태
     */
    case PARTIAL_CANCELED = 'PARTIAL_CANCELED';

    /**
     * 결제 승인 중 에러 발생 상태
     */
    case ABORTED = 'ABORTED';

    /**
     * 결제 유효 시간(30분) 만료 상태
     */
    case EXPIRED = 'EXPIRED';

    /**
     * 결제가 완료된 상태인지 확인
     */
    public function isCompleted(): bool
    {
        return $this === self::DONE;
    }

    /**
     * 결제가 취소된 상태인지 확인
     */
    public function isCanceled(): bool
    {
        return in_array($this, [self::CANCELED, self::PARTIAL_CANCELED], true);
    }

    /**
     * 결제가 실패한 상태인지 확인
     */
    public function isFailed(): bool
    {
        return in_array($this, [self::ABORTED, self::EXPIRED], true);
    }

    /**
     * 결제가 진행 중인 상태인지 확인
     */
    public function isPending(): bool
    {
        return in_array($this, [self::READY, self::IN_PROGRESS, self::WAITING_FOR_DEPOSIT], true);
    }
}

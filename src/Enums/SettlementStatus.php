<?php

declare(strict_types=1);

namespace Seandoesdev\TossPayments\Enums;

/**
 * 정산 상태 Enum
 *
 * @see https://docs.tosspayments.com/reference#%EC%A0%95%EC%82%B0
 */
enum SettlementStatus: string
{
    /**
     * 정산 완료
     */
    case COMPLETED = 'COMPLETED';

    /**
     * 정산 대기 중
     */
    case PENDING = 'PENDING';

    /**
     * 정산 취소
     */
    case CANCELED = 'CANCELED';

    /**
     * 정산이 완료되었는지 확인
     */
    public function isCompleted(): bool
    {
        return $this === self::COMPLETED;
    }

    /**
     * 정산이 대기 중인지 확인
     */
    public function isPending(): bool
    {
        return $this === self::PENDING;
    }

    /**
     * 정산이 취소되었는지 확인
     */
    public function isCanceled(): bool
    {
        return $this === self::CANCELED;
    }
}

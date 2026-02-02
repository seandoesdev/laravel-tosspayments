<?php

declare(strict_types=1);

namespace Seandoesdev\TossPayments\Enums;

/**
 * 현금영수증 타입 Enum
 *
 * @see https://docs.tosspayments.com/reference#%ED%98%84%EA%B8%88%EC%98%81%EC%88%98%EC%A6%9D
 */
enum CashReceiptType: string
{
    /**
     * 소득공제 (개인)
     */
    case INCOME_DEDUCTION = '소득공제';

    /**
     * 지출증빙 (사업자)
     */
    case EXPENSE_PROOF = '지출증빙';

    /**
     * 소득공제인지 확인
     */
    public function isIncomeDeduction(): bool
    {
        return $this === self::INCOME_DEDUCTION;
    }

    /**
     * 지출증빙인지 확인
     */
    public function isExpenseProof(): bool
    {
        return $this === self::EXPENSE_PROOF;
    }
}

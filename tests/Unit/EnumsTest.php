<?php

declare(strict_types=1);

use Seandoesdev\TossPayments\Enums\Bank;
use Seandoesdev\TossPayments\Enums\CardCompany;
use Seandoesdev\TossPayments\Enums\CashReceiptType;
use Seandoesdev\TossPayments\Enums\PaymentStatus;
use Seandoesdev\TossPayments\Enums\SettlementStatus;
use Seandoesdev\TossPayments\Enums\WebhookEventType;

describe('Bank Enum 테스트', function () {
    it('은행 이름을 반환할 수 있다', function () {
        expect(Bank::SHINHAN->label())->toBe('신한은행');
        expect(Bank::KOOKMIN->label())->toBe('국민은행');
        expect(Bank::KAKAOBANK->label())->toBe('카카오뱅크');
        expect(Bank::TOSSBANK->label())->toBe('토스뱅크');
    });

    it('은행 코드 값이 올바르다', function () {
        expect(Bank::SHINHAN->value)->toBe('88');
        expect(Bank::KOOKMIN->value)->toBe('04');
    });
});

describe('CardCompany Enum 테스트', function () {
    it('카드사 이름을 반환할 수 있다', function () {
        expect(CardCompany::SHINHAN->label())->toBe('신한카드');
        expect(CardCompany::KOOKMIN->label())->toBe('국민카드');
        expect(CardCompany::SAMSUNG->label())->toBe('삼성카드');
    });

    it('국내 카드사인지 확인할 수 있다', function () {
        expect(CardCompany::SHINHAN->isDomestic())->toBeTrue();
        expect(CardCompany::KOOKMIN->isDomestic())->toBeTrue();
        expect(CardCompany::VISA->isDomestic())->toBeFalse();
    });

    it('해외 카드사인지 확인할 수 있다', function () {
        expect(CardCompany::VISA->isInternational())->toBeTrue();
        expect(CardCompany::MASTER->isInternational())->toBeTrue();
        expect(CardCompany::SHINHAN->isInternational())->toBeFalse();
    });
});

describe('CashReceiptType Enum 테스트', function () {
    it('소득공제 타입을 확인할 수 있다', function () {
        expect(CashReceiptType::INCOME_DEDUCTION->isIncomeDeduction())->toBeTrue();
        expect(CashReceiptType::EXPENSE_PROOF->isIncomeDeduction())->toBeFalse();
    });

    it('지출증빙 타입을 확인할 수 있다', function () {
        expect(CashReceiptType::EXPENSE_PROOF->isExpenseProof())->toBeTrue();
        expect(CashReceiptType::INCOME_DEDUCTION->isExpenseProof())->toBeFalse();
    });
});

describe('PaymentStatus Enum 테스트', function () {
    it('완료 상태를 확인할 수 있다', function () {
        expect(PaymentStatus::DONE->isCompleted())->toBeTrue();
        expect(PaymentStatus::READY->isCompleted())->toBeFalse();
    });

    it('취소 상태를 확인할 수 있다', function () {
        expect(PaymentStatus::CANCELED->isCanceled())->toBeTrue();
        expect(PaymentStatus::PARTIAL_CANCELED->isCanceled())->toBeTrue();
        expect(PaymentStatus::DONE->isCanceled())->toBeFalse();
    });

    it('실패 상태를 확인할 수 있다', function () {
        expect(PaymentStatus::ABORTED->isFailed())->toBeTrue();
        expect(PaymentStatus::EXPIRED->isFailed())->toBeTrue();
        expect(PaymentStatus::DONE->isFailed())->toBeFalse();
    });

    it('진행중 상태를 확인할 수 있다', function () {
        expect(PaymentStatus::READY->isPending())->toBeTrue();
        expect(PaymentStatus::IN_PROGRESS->isPending())->toBeTrue();
        expect(PaymentStatus::WAITING_FOR_DEPOSIT->isPending())->toBeTrue();
        expect(PaymentStatus::DONE->isPending())->toBeFalse();
    });
});

describe('SettlementStatus Enum 테스트', function () {
    it('정산 상태를 확인할 수 있다', function () {
        expect(SettlementStatus::COMPLETED->isCompleted())->toBeTrue();
        expect(SettlementStatus::PENDING->isPending())->toBeTrue();
        expect(SettlementStatus::CANCELED->isCanceled())->toBeTrue();
    });
});

describe('WebhookEventType Enum 테스트', function () {
    it('결제 이벤트인지 확인할 수 있다', function () {
        expect(WebhookEventType::PAYMENT_STATUS_CHANGED->isPaymentEvent())->toBeTrue();
        expect(WebhookEventType::DEPOSIT_CALLBACK->isPaymentEvent())->toBeTrue();
        expect(WebhookEventType::BILLING_KEY_DELETED->isPaymentEvent())->toBeFalse();
    });

    it('입금 이벤트인지 확인할 수 있다', function () {
        expect(WebhookEventType::DEPOSIT_CALLBACK->isDepositEvent())->toBeTrue();
        expect(WebhookEventType::PAYMENT_STATUS_CHANGED->isDepositEvent())->toBeFalse();
    });

    it('빌링 이벤트인지 확인할 수 있다', function () {
        expect(WebhookEventType::BILLING_KEY_DELETED->isBillingEvent())->toBeTrue();
        expect(WebhookEventType::PAYMENT_STATUS_CHANGED->isBillingEvent())->toBeFalse();
    });
});

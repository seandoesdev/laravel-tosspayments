<?php

declare(strict_types=1);

namespace Seandoesdev\TossPayments\Enums;

/**
 * 카드사 코드 Enum
 *
 * @see https://docs.tosspayments.com/reference/codes#%EC%B9%B4%EB%93%9C%EC%82%AC-%EC%BD%94%EB%93%9C
 */
enum CardCompany: string
{
    case KOOKMIN = '11';
    case BC = '31';
    case HANA = '21';
    case SAMSUNG = '51';
    case SHINHAN = '41';
    case HYUNDAI = '61';
    case LOTTE = '71';
    case NH = '91';
    case CITI = '36';
    case SUHYUP = '35';
    case SHINHYUP = '62';
    case WOORI = '33';
    case GWANGJU = '46';
    case JEONBUK = '37';
    case JEJU = '38';
    case KAKAOBANK = '15';
    case KBANK = '3A';
    case TOSSBANK = '24';
    case SAEMAUL = '64';
    case POST = '63';
    case SAVINGBANK = '39';
    case VISA = '4V';
    case MASTER = '4M';
    case UNIONPAY = '3C';
    case AMEX = '7A';
    case JCB = '4J';
    case DINERS = '6D';
    case DISCOVER = '6I';

    /**
     * 카드사 이름 반환
     */
    public function label(): string
    {
        return match ($this) {
            self::KOOKMIN => '국민카드',
            self::BC => 'BC카드',
            self::HANA => '하나카드',
            self::SAMSUNG => '삼성카드',
            self::SHINHAN => '신한카드',
            self::HYUNDAI => '현대카드',
            self::LOTTE => '롯데카드',
            self::NH => 'NH농협카드',
            self::CITI => '씨티카드',
            self::SUHYUP => '수협카드',
            self::SHINHYUP => '신협카드',
            self::WOORI => '우리카드',
            self::GWANGJU => '광주카드',
            self::JEONBUK => '전북카드',
            self::JEJU => '제주카드',
            self::KAKAOBANK => '카카오뱅크',
            self::KBANK => '케이뱅크',
            self::TOSSBANK => '토스뱅크',
            self::SAEMAUL => '새마을금고',
            self::POST => '우체국',
            self::SAVINGBANK => '저축은행',
            self::VISA => 'VISA',
            self::MASTER => 'MASTER',
            self::UNIONPAY => '유니온페이',
            self::AMEX => 'AMEX',
            self::JCB => 'JCB',
            self::DINERS => 'DINERS',
            self::DISCOVER => 'DISCOVER',
        };
    }

    /**
     * 국내 카드사인지 확인
     */
    public function isDomestic(): bool
    {
        return in_array($this, [
            self::KOOKMIN,
            self::BC,
            self::HANA,
            self::SAMSUNG,
            self::SHINHAN,
            self::HYUNDAI,
            self::LOTTE,
            self::NH,
            self::CITI,
            self::SUHYUP,
            self::SHINHYUP,
            self::WOORI,
            self::GWANGJU,
            self::JEONBUK,
            self::JEJU,
            self::KAKAOBANK,
            self::KBANK,
            self::TOSSBANK,
            self::SAEMAUL,
            self::POST,
            self::SAVINGBANK,
        ], true);
    }

    /**
     * 해외 카드사인지 확인
     */
    public function isInternational(): bool
    {
        return in_array($this, [
            self::VISA,
            self::MASTER,
            self::UNIONPAY,
            self::AMEX,
            self::JCB,
            self::DINERS,
            self::DISCOVER,
        ], true);
    }
}

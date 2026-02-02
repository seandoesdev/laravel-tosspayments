<?php

declare(strict_types=1);

namespace Seandoesdev\TossPayments\Enums;

/**
 * 은행 코드 Enum
 *
 * @see https://docs.tosspayments.com/reference/codes#%EC%9D%80%ED%96%89-%EC%BD%94%EB%93%9C
 */
enum Bank: string
{
    case KYONGNAM = '39';
    case GWANGJU = '34';
    case KOOKMIN = '04';
    case IBK = '03';
    case SUHYUP = '07';
    case NH = '11';
    case WOORI = '20';
    case SC = '23';
    case CITI = '27';
    case DAEGU = '31';
    case BUSAN = '32';
    case SAEMAUL = '45';
    case SHINHAN = '88';
    case SHINHYUP = '48';
    case EPOST = '71';
    case JEONBUK = '37';
    case JEJU = '35';
    case KAKAOBANK = '90';
    case KBANK = '89';
    case TOSSBANK = '92';
    case HANA = '81';
    case KDB = '02';
    case KCIS = '54';

    /**
     * 은행 이름 반환
     */
    public function label(): string
    {
        return match ($this) {
            self::KYONGNAM => '경남은행',
            self::GWANGJU => '광주은행',
            self::KOOKMIN => '국민은행',
            self::IBK => '기업은행',
            self::SUHYUP => '수협은행',
            self::NH => '농협은행',
            self::WOORI => '우리은행',
            self::SC => 'SC제일은행',
            self::CITI => '씨티은행',
            self::DAEGU => '대구은행',
            self::BUSAN => '부산은행',
            self::SAEMAUL => '새마을금고',
            self::SHINHAN => '신한은행',
            self::SHINHYUP => '신협',
            self::EPOST => '우체국',
            self::JEONBUK => '전북은행',
            self::JEJU => '제주은행',
            self::KAKAOBANK => '카카오뱅크',
            self::KBANK => '케이뱅크',
            self::TOSSBANK => '토스뱅크',
            self::HANA => '하나은행',
            self::KDB => '산업은행',
            self::KCIS => '저축은행',
        };
    }
}

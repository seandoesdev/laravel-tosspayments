<?php

declare(strict_types=1);

namespace Seandoesdev\TossPayments\Contracts;

interface AttributeInterface
{
    /**
     * URI 초기화
     */
    public function initializeUri(): static;

    /**
     * 엔드포인트 생성
     */
    public function createEndpoint(?string $endpoint, bool $withUri = true): string;
}

<?php

declare(strict_types=1);

namespace Seandoesdev\TossPayments\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Seandoesdev\TossPayments\Exceptions\TossValidationException;
use Seandoesdev\TossPayments\Support\WebhookVerifier;

/**
 * Toss 웹훅 서명 검증 미들웨어
 */
class VerifyTossWebhookSignature
{
    public function __construct(
        private readonly WebhookVerifier $verifier,
    ) {
    }

    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $signature = $request->header('Toss-Signature');

        if (empty($signature)) {
            return response()->json([
                'error' => 'Missing Toss-Signature header',
            ], 401);
        }

        try {
            $this->verifier->verify(
                payload: $request->getContent(),
                signature: $signature,
            );
        } catch (TossValidationException $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'code' => $e->getErrorCode(),
            ], 401);
        }

        return $next($request);
    }
}

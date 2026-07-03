<?php

namespace App\Support;

use App\Models\Snippet;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class OwnerToken
{
    public const COOKIE_NAME = 'cortito_owner';

    public const COOKIE_DURATION = 365 * 24 * 60 * 60; // 1 año en segundos

    /**
     * Genera un nuevo owner token (UUID v4).
     */
    public static function generate(): string
    {
        return Str::uuid()->toString();
    }

    /**
     * Hashea el token para almacenamiento seguro en BD.
     */
    public static function hash(string $token): string
    {
        return hash('sha256', $token);
    }

    /**
     * Setea la cookie cortito_owner en la respuesta.
     */
    public static function setCookie(Response $response, string $token): Response
    {
        return $response->withCookie(cookie()->make(
            name: self::COOKIE_NAME,
            value: $token,
            minutes: self::COOKIE_DURATION,
            path: '/',
            secure: request()->secure(),
            httpOnly: true,
            sameSite: 'lax',
        ));
    }

    /**
     * Obtiene el token hasheado desde la cookie del request.
     * Retorna null si no existe la cookie.
     */
    public static function getHashFromRequest(Request $request): ?string
    {
        $token = $request->cookie(self::COOKIE_NAME);

        if (empty($token)) {
            return null;
        }

        return self::hash($token);
    }

    /**
     * Verifica si el request tiene la cookie de owner.
     */
    public static function hasCookie(Request $request): bool
    {
        return filled($request->cookie(self::COOKIE_NAME));
    }

    /**
     * Obtiene los snippets de un usuario anónimo basándose en la cookie.
     */
    public static function getSnippetsForRequest(Request $request)
    {
        $hash = self::getHashFromRequest($request);

        if ($hash === null) {
            return collect();
        }

        return Snippet::where('owner_token', $hash)
            ->whereNull('user_id')
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * Elimina la cookie cortito_owner.
     */
    public static function clearCookie(Response $response): Response
    {
        return $response->withCookie(cookie()->forget(self::COOKIE_NAME));
    }
}

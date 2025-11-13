<?php

declare(strict_types=1);

namespace Tests\Integration\Auth\Infrastructure;

use Tests\TestCase;
use Illuminate\Support\Facades\Lang;

class TranslationTest extends TestCase
{
    public function test_required_translations_exist(): void
    {
        // ⚠️ Desactivamos temporalmente el fallback para detectar traducciones faltantes
        $originalFallback = config('app.fallback_locale');
        config(['app.fallback_locale' => null]);

        // Claves exactas según tus archivos lang/es/messages.php y lang/en/messages.php
        $requiredTranslations = [
            'messages.user.registered_success',
            'messages.user.EMAIL_ALREADY_EXISTS', // clave con mayúsculas exactas
            'messages.user.EMPTY_USER_ID',
            'messages.user.INVALID_USER_ID_FORMAT',
            'messages.user.EMPTY_EMAIL',
            'messages.user.EMPTY_PASSWORD',
            'messages.user.EMPTY_NAME',
            'messages.user.INVALID_EMAIL_FORMAT',
            'messages.user.INVALID_USER_NAME',
            'messages.user.INVALID_PASSWORD',
            'messages.user.PASSWORD_CONFIRMATION_MISMATCH',
            'messages.unexpected_error',
        ];

        foreach ($requiredTranslations as $key) {
            // Comprueba si la traducción existe
            $translationExists = Lang::has($key);
            // Obtiene la traducción
            $translation = Lang::get($key);
            // Detecta si Laravel devolvió la clave (fallback implícito)
            $isUsingFallback = $translation === $key;

            $this->assertTrue(
                $translationExists && !$isUsingFallback,
                "Translation missing or using fallback for key: {$key} in locale: " . app()->getLocale()
            );
        }

        // Restaurar el fallback original
        config(['app.fallback_locale' => $originalFallback]);
    }
}

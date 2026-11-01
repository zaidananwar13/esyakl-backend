<?php

namespace App\Helpers;

class MediaHelper
{
    public static function getMediaUrl(?string $filename): ?string
    {
        if (empty($filename)) {
            return null;
        }
        
        $baseUrl = config('app-settings.media.base_url', 'https://admin.arabic-go.com/');
        $uploadPath = config('app-settings.media.upload_path', 'uploads/media/');
        
        return $baseUrl . $uploadPath . $filename;
    }

    public static function getAvatarUrl(?string $filename, string $default = 'user.png'): string
    {
        return self::getMediaUrl($filename ?? $default) ?? self::getMediaUrl($default);
    }

    public static function getCertificateTemplateUrl(?string $template): ?string
    {
        if (empty($template)) {
            return null;
        }
        
        $baseUrl = config('app-settings.media.base_url', 'https://admin.arabic-go.com/');
        $uploadPath = config('app-settings.certificate.upload_path', 'uploads/certificate/');
        
        return $baseUrl . $uploadPath . $template;
    }

    public static function getStorageUrl(?string $filename): ?string
    {
        if (empty($filename)) {
            return null;
        }
        
        $baseUrl = config('app-settings.storage.base_url', 'https://api.arabic-go.com/storage/');
        
        return $baseUrl . 'files/user/' . $filename;
    }

    public static function getFrontendUrl(string $path = '', bool $isLocal = false): string
    {
        if ($isLocal) {
            return config('app-settings.frontend.local_url', 'http://localhost:3000/') . ltrim($path, '/');
        }
        
        return config('app-settings.frontend.base_url', 'https://arabic-go.com/') . ltrim($path, '/');
    }
}

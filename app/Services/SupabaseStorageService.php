<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SupabaseStorageService
{
    protected string $url;
    protected string $key;
    protected string $bucket;

    public function __construct(?string $url = null, ?string $key = null, ?string $bucket = null)
    {
        $this->url = $url ?? config('supabase.url', '');
        $this->key = $key ?? config('supabase.key', '');
        $this->bucket = $bucket ?? config('supabase.bucket', 'games');
    }

    public function isConfigured(): bool
    {
        return !empty($this->url) && !empty($this->key);
    }

    public function upload(UploadedFile $file, ?string $path = null): string
    {
        if (!$this->isConfigured()) {
            throw new \RuntimeException('Supabase URL atau Key belum dikonfigurasi di file .env.');
        }

        $extension = $file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'png';
        $path = $path ?? (Str::uuid()->toString() . '.' . $extension);
        $path = ltrim($path, '/');

        $endpoint = rtrim($this->url, '/') . '/storage/v1/object/' . $this->bucket . '/' . $path;

        $mimeType = $file->getMimeType() ?: 'application/octet-stream';
        $fileContents = file_get_contents($file->getRealPath());

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->key,
            'apikey' => $this->key,
            'Content-Type' => $mimeType,
            'x-upsert' => 'true',
        ])->withBody($fileContents, $mimeType)
            ->post($endpoint);

        if (!$response->successful()) {
            $errorMessage = $response->json('message') ?? $response->json('error') ?? $response->body();
            throw new \RuntimeException("Gagal mengunggah gambar ke Supabase Storage: {$errorMessage}");
        }

        return $this->getPublicUrl($path);
    }

    public function getPublicUrl(string $path): string
    {
        return rtrim($this->url, '/') . '/storage/v1/object/public/' . $this->bucket . '/' . ltrim($path, '/');
    }

    public function delete(?string $urlOrPath): bool
    {
        if (empty($urlOrPath) || !$this->isConfigured()) {
            return false;
        }

        $path = $this->extractPath($urlOrPath);
        if (empty($path)) {
            return false;
        }

        $endpoint = rtrim($this->url, '/') . '/storage/v1/object/' . $this->bucket;

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->key,
                'apikey' => $this->key,
            ])->delete($endpoint, [
                        'prefixes' => [$path],
                    ]);

            return $response->successful();
        } catch (\Throwable $e) {
            report($e);
            return false;
        }
    }

    public function extractPath(string $urlOrPath): string
    {
        $publicPrefix = "/storage/v1/object/public/{$this->bucket}/";
        if (str_contains($urlOrPath, $publicPrefix)) {
            return Str::after($urlOrPath, $publicPrefix);
        }

        $objectPrefix = "/storage/v1/object/{$this->bucket}/";
        if (str_contains($urlOrPath, $objectPrefix)) {
            return Str::after($urlOrPath, $objectPrefix);
        }

        if (filter_var($urlOrPath, FILTER_VALIDATE_URL)) {
            $parsed = parse_url($urlOrPath, PHP_URL_PATH);
            if ($parsed && str_contains($parsed, "/{$this->bucket}/")) {
                return Str::after($parsed, "/{$this->bucket}/");
            }
            return basename($urlOrPath);
        }

        return ltrim($urlOrPath, '/');
    }
}

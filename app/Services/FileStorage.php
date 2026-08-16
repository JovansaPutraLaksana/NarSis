<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class FileStorage
{
    public function store(UploadedFile $file, string $directory): string
    {
        $extension = strtolower($file->getClientOriginalExtension() ?: 'bin');
        $path = trim($directory, '/').'/'.Str::uuid().'.'.$extension;

        if ($this->usesSupabase()) {
            $mime = $file->getMimeType() ?: 'application/octet-stream';
            Http::withHeaders($this->headers())
                ->withHeader('Content-Type', $mime)
                ->withHeader('x-upsert', 'false')
                ->withBody(file_get_contents($file->getRealPath()), $mime)
                ->post($this->objectUrl($path))
                ->throw();
            return $path;
        }

        $stored = Storage::disk('local')->putFileAs(dirname($path), $file, basename($path));
        abort_unless($stored !== false, 500, 'File gagal disimpan.');

        return $path;
    }

    public function delete(?string $path): void
    {
        if (! $path) return;

        if ($this->usesSupabase()) {
            Http::withHeaders($this->headers())
                ->delete($this->baseUrl().'/storage/v1/object/'.$this->bucket(), ['prefixes' => [$path]])
                ->throw();
            return;
        }

        Storage::disk('local')->delete($path);
    }

    public function download(string $path, string $downloadName): Response
    {
        if ($this->usesSupabase()) {
            $response = Http::withHeaders($this->headers())
                ->post($this->baseUrl().'/storage/v1/object/sign/'.$this->bucket().'/'.$this->encodePath($path), ['expiresIn' => 60]);

            abort_if($response->status() === 404, 404);
            $response->throw();
            $signed = $response->json('signedURL') ?? $response->json('signedUrl');
            abort_unless($signed, 404);

            $url = str_starts_with($signed, 'http') ? $signed : $this->baseUrl().'/storage/v1'.$signed;
            $url .= (str_contains($url, '?') ? '&' : '?').'download='.rawurlencode($downloadName);
            return redirect()->away($url);
        }

        abort_unless(Storage::disk('local')->exists($path), 404);
        return Storage::disk('local')->download($path, $downloadName);
    }

    private function usesSupabase(): bool
    {
        return config('narsis.storage.driver') === 'supabase';
    }

    private function baseUrl(): string
    {
        $url = rtrim((string) config('narsis.storage.supabase_url'), '/');
        abort_if($url === '', 500, 'SUPABASE_URL belum dikonfigurasi.');
        return $url;
    }

    private function bucket(): string
    {
        return rawurlencode((string) config('narsis.storage.supabase_bucket'));
    }

    private function objectUrl(string $path): string
    {
        return $this->baseUrl().'/storage/v1/object/'.$this->bucket().'/'.$this->encodePath($path);
    }

    private function headers(): array
    {
        $key = (string) config('narsis.storage.supabase_service_key');
        abort_if($key === '', 500, 'SUPABASE_SERVICE_ROLE_KEY belum dikonfigurasi.');
        return ['Authorization' => 'Bearer '.$key, 'apikey' => $key];
    }

    private function encodePath(string $path): string
    {
        return collect(explode('/', trim($path, '/')))->map(fn ($segment) => rawurlencode($segment))->implode('/');
    }
}

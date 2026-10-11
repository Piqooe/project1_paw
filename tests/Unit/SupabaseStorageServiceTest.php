<?php

namespace Tests\Unit;

use App\Services\SupabaseStorageService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SupabaseStorageServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config([
            'supabase.url' => 'https://example-project.supabase.co',
            'supabase.key' => 'test-supabase-key',
            'supabase.bucket' => 'games',
        ]);
    }

    public function test_can_upload_file_to_supabase_storage(): void
    {
        Http::fake([
            'https://example-project.supabase.co/storage/v1/object/games/*' => Http::response([
                'Key' => 'games/test-image.jpg',
            ], 200),
        ]);

        $service = new SupabaseStorageService();
        $file = UploadedFile::fake()->image('cover.jpg', 600, 400);

        $url = $service->upload($file, 'cover.jpg');

        $this->assertEquals(
            'https://example-project.supabase.co/storage/v1/object/public/games/cover.jpg',
            $url
        );

        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/storage/v1/object/games/cover.jpg') &&
                   $request->hasHeader('Authorization', 'Bearer test-supabase-key') &&
                   $request->hasHeader('apikey', 'test-supabase-key');
        });
    }

    public function test_upload_throws_exception_when_not_configured(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Supabase URL atau Key belum dikonfigurasi');

        $service = new SupabaseStorageService('', '', 'games');
        $file = UploadedFile::fake()->image('cover.jpg');
        $service->upload($file);
    }

    public function test_upload_throws_exception_on_supabase_error(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Gagal mengunggah gambar ke Supabase Storage: Bucket not found');

        Http::fake([
            'https://example-project.supabase.co/storage/v1/object/games/*' => Http::response([
                'message' => 'Bucket not found',
            ], 404),
        ]);

        $service = new SupabaseStorageService();
        $file = UploadedFile::fake()->image('cover.jpg');
        $service->upload($file);
    }

    public function test_can_delete_file_from_supabase_storage(): void
    {
        Http::fake([
            'https://example-project.supabase.co/storage/v1/object/games' => Http::response([
                'message' => 'Successfully deleted',
            ], 200),
        ]);

        $service = new SupabaseStorageService();
        $url = 'https://example-project.supabase.co/storage/v1/object/public/games/games-123.jpg';

        $result = $service->delete($url);

        $this->assertTrue($result);

        Http::assertSent(function ($request) {
            return $request->url() === 'https://example-project.supabase.co/storage/v1/object/games' &&
                   $request->method() === 'DELETE' &&
                   $request['prefixes'] === ['games-123.jpg'];
        });
    }

    public function test_extract_path_handles_various_url_formats(): void
    {
        $service = new SupabaseStorageService('https://example-project.supabase.co', 'key', 'games');

        $this->assertEquals(
            'covers/zelda.jpg',
            $service->extractPath('https://example-project.supabase.co/storage/v1/object/public/games/covers/zelda.jpg')
        );

        $this->assertEquals(
            'zelda.jpg',
            $service->extractPath('zelda.jpg')
        );
    }
}

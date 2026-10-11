<?php

namespace Tests\Feature;

use App\Models\Game;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GameApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->unverified()->create());
    }

    public function test_unverified_user_can_access_protected_features(): void
    {
        $user = User::factory()->unverified()->create();

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertOk();

        $this->actingAs($user)
            ->getJson('/games')
            ->assertOk();
    }

    public function test_can_get_all_games(): void
    {
        $response = $this->getJson('/games');

        $response->assertStatus(200);
    }

    public function test_can_create_game(): void
    {
        $payload = [
            'nama' => 'God of War Ragnarok',
            'developer' => 'Santa Monica Studio',
            'tahun' => 2022,
            'rilis' => '2022-11-09',
            'genre' => 'Action-Adventure',
            'harga' => 879000,
        ];

        $response = $this->postJson('/games', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('message', 'Data game berhasil ditambahkan.')
            ->assertJsonPath('data.nama', 'God of War Ragnarok');

        $this->assertDatabaseHas('games', [
            'nama' => 'God of War Ragnarok',
        ]);
    }

    public function test_can_show_game(): void
    {
        $game = Game::create([
            'nama' => 'Spider-Man 2',
            'developer' => 'Insomniac Games',
            'tahun' => 2023,
            'rilis' => '2023-10-20',
            'genre' => 'Action',
            'harga' => 1029000,
        ]);

        $response = $this->getJson("/games/{$game->id}");

        $response->assertStatus(200)
            ->assertJsonPath('nama', 'Spider-Man 2');
    }

    public function test_can_update_game(): void
    {
        $game = Game::create([
            'nama' => 'Cyberpunk 2077',
            'developer' => 'CD Projekt Red',
            'tahun' => 2020,
            'rilis' => '2020-12-10',
            'genre' => 'RPG',
            'harga' => 699000,
        ]);

        $response = $this->putJson("/games/{$game->id}", [
            'harga' => 350000,
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('message', 'Data game berhasil diperbarui.')
            ->assertJsonPath('data.harga', 350000);
    }

    public function test_can_delete_game(): void
    {
        $game = Game::create([
            'nama' => 'Game to Delete',
            'developer' => 'Tester',
            'tahun' => 2024,
            'rilis' => '2024-01-01',
            'genre' => 'Test',
            'harga' => 10000,
        ]);

        $response = $this->deleteJson("/games/{$game->id}");

        $response->assertStatus(200)
            ->assertJsonPath('message', 'Data game berhasil dihapus.');

        $this->assertDatabaseMissing('games', [
            'id' => $game->id,
        ]);
    }

    public function test_create_and_edit_routes_do_not_exist(): void
    {
        $createResponse = $this->get('/games/create');
        $createResponse->assertStatus(404);
    }

    public function test_can_create_game_with_image(): void
    {
        config([
            'supabase.url' => 'https://example-project.supabase.co',
            'supabase.key' => 'test-key',
            'supabase.bucket' => 'games',
        ]);

        \Illuminate\Support\Facades\Http::fake([
            'https://example-project.supabase.co/storage/v1/object/games/*' => \Illuminate\Support\Facades\Http::response([
                'Key' => 'games/god-of-war.jpg',
            ], 200),
        ]);

        $file = \Illuminate\Http\UploadedFile::fake()->image('god-of-war.jpg', 600, 600);

        $payload = [
            'nama' => 'God of War',
            'developer' => 'Santa Monica Studio',
            'tahun' => 2018,
            'rilis' => '2018-04-20',
            'genre' => 'Action-Adventure',
            'harga' => 499000,
            'gambar' => $file,
        ];

        $response = $this->post('/games', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('message', 'Data game berhasil ditambahkan.');

        $game = Game::where('nama', 'God of War')->first();
        $this->assertNotNull($game);
        $this->assertNotNull($game->gambar);
        $this->assertStringStartsWith('https://example-project.supabase.co/storage/v1/object/public/games/', $game->gambar);
    }

    public function test_can_update_game_with_new_image(): void
    {
        config([
            'supabase.url' => 'https://example-project.supabase.co',
            'supabase.key' => 'test-key',
            'supabase.bucket' => 'games',
        ]);

        \Illuminate\Support\Facades\Http::fake([
            'https://example-project.supabase.co/storage/v1/object/games/*' => \Illuminate\Support\Facades\Http::response([
                'Key' => 'games/elden-ring-new.jpg',
            ], 200),
            'https://example-project.supabase.co/storage/v1/object/games' => \Illuminate\Support\Facades\Http::response([
                'message' => 'Successfully deleted',
            ], 200),
        ]);

        $game = Game::create([
            'nama' => 'Elden Ring',
            'developer' => 'FromSoftware',
            'tahun' => 2022,
            'rilis' => '2022-02-25',
            'genre' => 'Action RPG',
            'harga' => 599000,
            'gambar' => 'https://example-project.supabase.co/storage/v1/object/public/games/old-image.jpg',
        ]);

        $newFile = \Illuminate\Http\UploadedFile::fake()->image('elden-ring-new.jpg');

        $response = $this->put("/games/{$game->id}", [
            'nama' => 'Elden Ring Shadow of the Erdtree',
            'gambar' => $newFile,
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('message', 'Data game berhasil diperbarui.');

        $game->refresh();
        $this->assertEquals('Elden Ring Shadow of the Erdtree', $game->nama);
        $this->assertNotEquals('https://example-project.supabase.co/storage/v1/object/public/games/old-image.jpg', $game->gambar);
    }

    public function test_deleting_game_removes_image_from_supabase(): void
    {
        config([
            'supabase.url' => 'https://example-project.supabase.co',
            'supabase.key' => 'test-key',
            'supabase.bucket' => 'games',
        ]);

        \Illuminate\Support\Facades\Http::fake([
            'https://example-project.supabase.co/storage/v1/object/games' => \Illuminate\Support\Facades\Http::response([
                'message' => 'Successfully deleted',
            ], 200),
        ]);

        $game = Game::create([
            'nama' => 'Hades',
            'developer' => 'Supergiant Games',
            'tahun' => 2020,
            'rilis' => '2020-09-17',
            'genre' => 'Roguelike',
            'harga' => 250000,
            'gambar' => 'https://example-project.supabase.co/storage/v1/object/public/games/hades.jpg',
        ]);

        $response = $this->deleteJson("/games/{$game->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('games', ['id' => $game->id]);

        \Illuminate\Support\Facades\Http::assertSent(function ($request) {
            return $request->url() === 'https://example-project.supabase.co/storage/v1/object/games' &&
                   $request->method() === 'DELETE' &&
                   $request['prefixes'] === ['hades.jpg'];
        });
    }

    public function test_rejects_invalid_image_file_type(): void
    {
        $invalidFile = \Illuminate\Http\UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

        $payload = [
            'nama' => 'Invalid Game',
            'developer' => 'Test',
            'tahun' => 2024,
            'rilis' => '2024-01-01',
            'genre' => 'Test',
            'harga' => 10000,
            'gambar' => $invalidFile,
        ];

        $response = $this->postJson('/games', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['gambar']);
    }
}

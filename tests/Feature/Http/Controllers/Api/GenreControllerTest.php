<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Support\Facades\Lang;
use Tests\TestCase;

class GenreControllerTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @return void
     */
    public function testIndex()
    {
        $genre = factory(Genre::class)->create();
        $response = $this->get(route('genres.index'));
        $response->assertStatus(200)
                 ->assertJson([$genre->toArray()]);
    }

    /**
     * @return void
     */
    public function testShow()
    {
        $genre = factory(Genre::class)->create();
        $response = $this->get(route('genres.show', $genre->id));
        $response->assertStatus(200)
                 ->assertJson($genre->toArray());
    }

    /**
     * @return void
     */
    public function testStore()
    {
        $response = $this->json('POST', route('genres.store'), [
            'name' => 'test'
        ]);
        $id = $response->json('id');
        $genre = Genre::find($id);
        $response->assertStatus(201)
                 ->assertJson($genre->toArray());
        $this->assertTrue($response->json('is_active'));

        $response = $this->json('POST', route('genres.store'), [
            'name' => 'test',
            'is_active' => false,
        ]);
        $response->assertJsonFragment([
            'is_active' => false,
        ]);
    }
    
    /**
     * @return void
     */
    public function testUpdate()
    {
        $genre = factory(Genre::class)->create(['is_active' => false]);
        $response = $this->json('PUT', route('genres.update', ['genre' => $genre->id]), [
            'name' => 'test',
            'is_active' => true,
        ]);
        $id = $response->json('id');
        $genre = Genre::find($id);
        $response->assertStatus(200)
                 ->assertJson($genre->toArray())
                 ->assertJsonFragment(['is_active' => true]);
        $response = $this->json('PUT', route('genres.update', ['genre' => $genre->id]), [
            'name' => 'test',
        ]);
    }

    public function testDestroy()
    {
        $genre = factory(Genre::class)->create();
        $response = $this->json('DELETE', route('genres.destroy', ['genre' => $genre->id]));
        $response->assertStatus(204);
        $this->assertNull(Genre::find($genre->id));
        $this->assertNotNull(Genre::withTrashed()->find($genre->id));
    }

    /**
     * @return void
     */
    public function testInvalidatorData()
    {
        $response = $this->json('POST', route('genres.store'), []);
        $this->assertInvalidationRequired($response);

        $response = $this->json('POST', route('genres.store'), [
            'name' => str_repeat('a', 256),
            'is_active' => 'c'
        ]);
        $this->assertInvalidationMax($response);
        $this->assertInvalidationBoolean($response);

        $genre = factory(genre::class)->create()->id;
        $response = $this->json('PUT', route('genres.update', compact('genre')), []);
        $this->assertInvalidationRequired($response);

        $response = $this->json('PUT', route('genres.update', compact('genre')), [
            'name' => str_repeat('a', 256),
            'is_active' => 'c'
        ]);
        $this->assertInvalidationMax($response);
        $this->assertInvalidationBoolean($response);
    }

    /**
     * @param TestResponse $response
     */
    private function assertInvalidationRequired(TestResponse $response)
    {
        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['name'])
                 ->assertJsonMissingValidationErrors(['is_active'])
                 ->assertJsonFragment([Lang::get('validation.required', ['attribute' => 'name', 'max' => 255])]);
    }

    /**
     * @param TestResponse $response
     */
    private function assertInvalidationMax(TestResponse $response)
    {
        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['name'])
                 ->assertJsonFragment([Lang::get('validation.max.string', ['attribute' => 'name', 'max' => 255])]);
    }

    /**
     * @param TestResponse $response
     */
    private function assertInvalidationBoolean(TestResponse $response)
    {
        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['is_active'])
                 ->assertJsonFragment([Lang::get('validation.boolean', ['attribute' => 'is active'])]);
    }
}

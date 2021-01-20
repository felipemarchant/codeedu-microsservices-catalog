<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Support\Facades\Lang;
use Tests\TestCase;
use Tests\Traits\TestValidations;

class GenreControllerTest extends TestCase
{
    use DatabaseMigrations;
    use TestValidations;

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
        $data = ['name' => ''];
        $this->assertInvalidationInStoreAction($data, 'required');
        $data = ['name' => str_repeat('a', 256)];
        $this->assertInvalidationInStoreAction($data, 'max.string', ['max' => 255]);
        $data = ['is_active' => 'c'];
        $this->assertInvalidationInStoreAction($data, 'boolean');
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
        $this->assertInvalidationFields($response, ['name'], 'required');
        $response->assertJsonMissingValidationErrors(['is_active']);
    }

    /**
     * @param TestResponse $response
     */
    private function assertInvalidationMax(TestResponse $response)
    {
        $this->assertInvalidationFields($response, ['name'], 'max.string', ['max' => 255]);
    }

    /**
     * @param TestResponse $response
     */
    private function assertInvalidationBoolean(TestResponse $response)
    {
        $this->assertInvalidationFields($response, ['is_active'], 'boolean');
    }

    /**
     *  @return string
     */
    protected function routeStore(): string
    {
        return route('categories.store'); 
    }
}

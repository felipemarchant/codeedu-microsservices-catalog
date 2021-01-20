<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\Traits\{TestValidations, TestSaves};

class GenreControllerTest extends TestCase
{
    use DatabaseMigrations, TestValidations, TestSaves;

    private $genre;

    protected function setUp(): void
    {
        parent::setUp();
        $this->genre = factory(Genre::class)->create();
    }

    /**
     * @return void
     */
    public function testIndex()
    {
        $response = $this->get(route('genres.index'));
        $response->assertStatus(200)
                 ->assertJson([$this->genre->toArray()]);
    }

    /**
     * @return void
     */
    public function testShow()
    {
        
        $response = $this->get(route('genres.show', $this->genre->id));
        $response->assertStatus(200)
                 ->assertJson($this->genre->toArray());
    }

    /**
     * @return void
     */
    public function testStore()
    {
        $data = ['name' => 'test'];
        $response = $this->assertStore($data, $data + ['is_active' => true, 'deleted_at' => null]);
        $response->assertJsonStructure(['created_at', 'updated_at']);
        $data = ['name' => 'test', 'is_active' => false];
        $response = $this->assertStore($data, $data + ['is_active' => false, 'deleted_at' => null]);
        $response->assertJsonStructure(['created_at', 'updated_at']);
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
        $response = $this->json('DELETE', route('genres.destroy', ['genre' => $this->genre->id]));
        $response->assertStatus(204);
        $this->assertNull(Genre::find($this->genre->id));
        $this->assertNotNull(Genre::withTrashed()->find($this->genre->id));
    }

    /**
     * @return void
     */
    public function testInvalidatorData()
    {
        $data = ['name' => ''];
        $this->assertInvalidationInStoreAction($data, 'required');
        $this->assertInvalidationInUpdateAction($data, 'required');
        $data = ['name' => str_repeat('a', 256)];
        $this->assertInvalidationInStoreAction($data, 'max.string', ['max' => 255]);
        $this->assertInvalidationInUpdateAction($data, 'max.string', ['max' => 255]);
        $data = ['is_active' => 'c'];
        $this->assertInvalidationInStoreAction($data, 'boolean');
        $this->assertInvalidationInUpdateAction($data, 'boolean');
    }

    /**
     *  @return string
     */
    protected function routeStore(): string
    {
        return route('genres.store'); 
    }

    /**
     *  @return string
     */
    protected function routeUpdate(): string
    {
        return route('genres.update', ['genre' => $this->genre]); 
    }

    /**
     *  @return string
     */
    protected function model(): string
    {
        return Genre::class; 
    }
}

<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Support\Facades\Lang;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @return void
     */
    public function testIndex()
    {
        $category = factory(Category::class)->create();
        $response = $this->get(route('categories.index'));
        $response->assertStatus(200)
                 ->assertJson([$category->toArray()]);
    }

    /**
     * @return void
     */
    public function testShow()
    {
        $category = factory(Category::class)->create();
        $response = $this->get(route('categories.show', $category->id));
        $response->assertStatus(200)
                 ->assertJson($category->toArray());
    }

    /**
     * @return void
     */
    public function testStore()
    {
        $response = $this->json('POST', route('categories.store'), [
            'name' => 'test'
        ]);
        $id = $response->json('id');
        $category = Category::find($id);
        $response->assertStatus(201)
                 ->assertJson($category->toArray());
        $this->assertTrue($response->json('is_active'));
        $this->assertNull($response->json('description'));

        $response = $this->json('POST', route('categories.store'), [
            'name' => 'test',
            'is_active' => false,
            'description' => 'description'
        ]);
        $response->assertJsonFragment([
            'is_active' => false,
            'description' => 'description'
        ]);
    }
    
    /**
     * @return void
     */
    public function testUpdate()
    {
        $category = factory(Category::class)->create(['is_active' => false, 'description' => 'desc']);
        $response = $this->json('PUT', route('categories.update', ['category' => $category->id]), [
            'name' => 'test',
            'is_active' => true,
            'description' => 'teste'
        ]);
        $id = $response->json('id');
        $category = Category::find($id);
        $response->assertStatus(200)
                 ->assertJson($category->toArray())
                 ->assertJsonFragment(['is_active' => true, 'description' => 'teste']);
        $response = $this->json('PUT', route('categories.update', ['category' => $category->id]), [
            'name' => 'test',
            'description' => ''
        ]);
        $response->assertJsonFragment([
            'description' => null
        ]);
    }

    /**
     * @return void
     */
    public function testInvalidatorData()
    {
        $response = $this->json('POST', route('categories.store'), []);
        $this->assertInvalidationRequired($response);

        $response = $this->json('POST', route('categories.store'), [
            'name' => str_repeat('a', 256),
            'is_active' => 'c'
        ]);
        $this->assertInvalidationMax($response);
        $this->assertInvalidationBoolean($response);

        $category = factory(Category::class)->create()->id;
        $response = $this->json('PUT', route('categories.update', compact('category')), []);
        $this->assertInvalidationRequired($response);

        $response = $this->json('PUT', route('categories.update', compact('category')), [
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

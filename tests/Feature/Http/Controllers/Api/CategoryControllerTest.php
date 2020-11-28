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

        $category = factory(Category::class)->create();
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

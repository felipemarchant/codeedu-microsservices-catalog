<?php

namespace Tests\Feature\Models;

use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * Teste listagem de categorias
     * @return void
     */
    public function testList()
    {
        factory(Category::class, 1)->create();
        $categories = Category::all();
        $this->assertCount(1, $categories);
        $categoryKey = array_keys($categories->first()->getAttributes());
        $this->assertEqualsCanonicalizing([
            'id',
            'name',
            'description',
            'is_active',
            'created_at',
            'updated_at',
            'deleted_at'
        ], $categoryKey);
    }

    /**
     * Testar criação da categoria
     * @return void
     */
    public function testCreate()
    {
        $category = Category::create(['name' => 'Teste 1'])->refresh();
        $this->assertEquals($category->name, 'Teste 1');
        $this->assertNull($category->description);
        $this->assertTrue($category->is_active);
        $category = Category::create(['name' => 'Teste 1', 'description' => null])->refresh();
        $this->assertNull($category->description);
        $category = Category::create(['name' => 'Teste 1', 'description' => 'Descrição'])->refresh();
        $this->assertEquals($category->description, 'Descrição');
        $category = Category::create(['name' => 'Teste 1', 'is_active' => false])->refresh();
        $this->assertFalse($category->is_active);
        $category = Category::create(['name' => 'Teste 1', 'is_active' => true])->refresh();
        $this->assertTrue($category->is_active);
        $this->assertTrue(Uuid::isValid($category->id));
        
    }

    /**
     * Testar update da categoria
     * @return void
     */
    public function testUpdate()
    {
        $category = factory(Category::class)->create(['description' => 'test description', 'is_active' => false])->first();
        $data = ['name' => '1111', 'description' => '2222', 'is_active' => true];
        $category->update($data);
        foreach ($data as $key => $value) $this->assertEquals($value, $category->{$key});
    }

    /**
     * @return void
     */
    public function testDelete()
    {
        $this->assertCount(0, Category::all());
        $category = factory(Category::class)->create(['description' => 'test description', 'is_active' => false])->first();
        $category->delete();
        $this->assertCount(0, Category::all());
        $category = factory(Category::class)->create(['description' => 'test description', 'is_active' => false])->first();
        Category::destroy($category->id);
        $this->assertCount(0, Category::all());
    }
}

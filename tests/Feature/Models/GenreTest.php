<?php

namespace Tests\Feature\Models;

use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class GenreTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * Teste listagem de categorias
     * @return void
     */
    public function testList()
    {
        factory(Genre::class, 1)->create();
        $genres = Genre::all();
        $this->assertCount(1, $genres);
        $genreKey = array_keys($genres->first()->getAttributes());
        $this->assertEqualsCanonicalizing([
            'id',
            'name',
            'is_active',
            'created_at',
            'updated_at',
            'deleted_at'
        ], $genreKey);
    }

    /**
     * Testar criação da categoria
     * @return void
     */
    public function testCreate()
    {
        $genre = Genre::create(['name' => 'Teste 1'])->refresh();
        $this->assertEquals($genre->name, 'Teste 1');
        $this->assertTrue($genre->is_active);
        $genre = Genre::create(['name' => 'Teste 1', 'is_active' => false])->refresh();
        $this->assertFalse($genre->is_active);
        $genre = Genre::create(['name' => 'Teste 1', 'is_active' => true])->refresh();
        $this->assertTrue($genre->is_active);
        $this->assertTrue(Uuid::isValid($genre->id));
    }
}

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
     * Testar listagem de gêneros
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
     * Testar criação da gênero
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

    /**
     * Testar update da gênero
     * @return void
     */
    public function testUpdate()
    {
        $genre = factory(Genre::class)->create(['name' => 'test name', 'is_active' => true])->first();
        $data = ['name' => '1111', 'is_active' => false];
        $genre->update($data);
        foreach ($data as $key => $value) $this->assertEquals($value, $genre->{$key});
    }

    /**
     * Testar delete de gênero
     * @return void
     */
    public function testDelete()
    {
        $this->assertCount(0, Genre::all());
        $genre = factory(Genre::class)->create()->first();
        $genre->delete();
        $this->assertCount(0, Genre::all());
        $genre = factory(Genre::class)->create()->first();
        Genre::destroy($genre->id);
        $this->assertCount(0, Genre::all());
    }
}

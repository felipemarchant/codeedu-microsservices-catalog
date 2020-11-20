<?php

use Illuminate\Database\Seeder;

class GenreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        App\Models\Genre::create(['name' => 'Terror']);
        App\Models\Genre::create(['name' => 'Ação']);
        App\Models\Genre::create(['name' => 'Comédia']);
    }
}

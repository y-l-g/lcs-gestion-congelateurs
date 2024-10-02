<?php

namespace Database\Seeders;

use App\Models\Produit;
use App\Models\Stock;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'youenn',
            'email' => 'youenn.legouedec@gmail.com',
        ]);

        Produit::factory(6)->sequence(
            ['nom' => 'Patates'],
            ['nom' => 'Carottes'],
            ['nom' => 'Poireaux'],
            ['nom' => 'Fromage'],
            ['nom' => 'Pommes abimées'],
            ['nom' => "Jus d'orange frais"]
        )
            ->create();

        Stock::factory(100)->create();
    }
}

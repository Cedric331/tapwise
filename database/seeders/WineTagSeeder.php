<?php

namespace Database\Seeders;

use App\Models\WineTag;
use Illuminate\Database\Seeder;

class WineTagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            // Fruits
            ['name' => 'Fruits rouges', 'slug' => 'fruits_rouges'],
            ['name' => 'Fruits noirs', 'slug' => 'fruits_noirs'],
            ['name' => 'Fruits blancs', 'slug' => 'fruits_blancs'],
            ['name' => 'Agrumes', 'slug' => 'agrumes'],
            ['name' => 'Fruits exotiques', 'slug' => 'fruits_exotiques'],

            // Arômes
            ['name' => 'Floral', 'slug' => 'floral'],
            ['name' => 'Boisé', 'slug' => 'boise'],
            ['name' => 'Épicé', 'slug' => 'epice'],
            ['name' => 'Minéral', 'slug' => 'mineral'],
            ['name' => 'Fumé', 'slug' => 'fume'],
            ['name' => 'Vanillé', 'slug' => 'vanille'],

            // Sensations
            ['name' => 'Sec', 'slug' => 'sec'],
            ['name' => 'Rond', 'slug' => 'rond'],
            ['name' => 'Tannique', 'slug' => 'tannique'],
            ['name' => 'Sucré', 'slug' => 'sucre'],
        ];

        foreach ($tags as $tag) {
            WineTag::firstOrCreate(['slug' => $tag['slug']], $tag);
        }
    }
}


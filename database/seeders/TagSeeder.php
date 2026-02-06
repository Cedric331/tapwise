<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            // Saveurs
            ['name' => 'Fruits', 'slug' => 'fruits'],
            ['name' => 'Agrumes', 'slug' => 'agrumes'],
            ['name' => 'Torréfié', 'slug' => 'torrefie'],
            ['name' => 'Malté', 'slug' => 'malte'],
            ['name' => 'Épicé', 'slug' => 'epice'],
            ['name' => 'Acidulé', 'slug' => 'acidule'],

            // Arômes
            ['name' => 'Florale', 'slug' => 'florale'],
            ['name' => 'Herbacé', 'slug' => 'herbace'],
            ['name' => 'Résineux', 'slug' => 'resineux'],
            ['name' => 'Caramélisé', 'slug' => 'caramelise'],
            ['name' => 'Chocolaté', 'slug' => 'chocolate'],
            ['name' => 'Café', 'slug' => 'cafe'],

            // Sensations
            ['name' => 'Amère', 'slug' => 'amere'],
            ['name' => 'Sèche', 'slug' => 'seche'],
            ['name' => 'Ronde', 'slug' => 'ronde'],
            ['name' => 'Crémeuse', 'slug' => 'cremeuse'],
            ['name' => 'Puissante', 'slug' => 'puissante'],

            // Craft
            ['name' => 'Houblonnée', 'slug' => 'houblonnee'],
            ['name' => 'Juteuse', 'slug' => 'juteuse'],
            ['name' => 'Sauvage', 'slug' => 'sauvage'],
        ];

        foreach ($tags as $tag) {
            Tag::firstOrCreate(['slug' => $tag['slug']], $tag);
        }
    }
}

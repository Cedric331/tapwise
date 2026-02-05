<?php

namespace Database\Seeders;

use App\Models\Bar;
use App\Models\Beer;
use App\Models\Tag;
use Illuminate\Database\Seeder;

class DemoBarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bar = Bar::firstOrCreate(
            ['slug' => 'demo'],
            [
                'name' => 'Tapwise Demo Bar',
                'slug' => 'demo',
                'logo_path' => 'assets/logo.svg',
                'brand_background_color' => '#ffffff',
                'brand_primary_color' => '#f59e0b',
                'welcome_message' => 'Bienvenue ! Répondez à quelques questions pour découvrir la bière parfaite pour vous.',
                'qr_enabled' => true,
                'is_demo' => true,
            ]
        );

        // Get tags
        $tags = Tag::all()->keyBy('slug');

        $beers = [
            [
                'name' => 'IPA Citra',
                'brewery' => 'Brasserie du Nord',
                'style' => 'IPA',
                'color' => 'golden',
                'abv_x10' => 65,
                'ibu' => 65,
                'description' => 'Une IPA moderne aux arômes d\'agrumes et de fruits tropicaux.',
                'is_on_tap' => true,
                'is_available' => true,
                'price' => 650,
                'tags' => ['agrumes', 'fruits'],
            ],
            [
                'name' => 'Stout Chocolat',
                'brewery' => 'Brasserie Artisanale',
                'style' => 'Stout',
                'color' => 'black',
                'abv_x10' => 55,
                'ibu' => 40,
                'description' => 'Une stout onctueuse aux notes de chocolat noir et de café torréfié.',
                'is_on_tap' => true,
                'is_available' => true,
                'price' => 700,
                'tags' => ['torrefie', 'malte'],
            ],
            [
                'name' => 'Blonde Tradition',
                'brewery' => 'Brasserie Classique',
                'style' => 'Blonde',
                'color' => 'blonde',
                'abv_x10' => 50,
                'ibu' => 20,
                'description' => 'Une blonde équilibrée aux notes maltées et légèrement fruitées.',
                'is_on_tap' => true,
                'is_available' => true,
                'price' => 550,
                'tags' => ['malte', 'fruits'],
            ],
            [
                'name' => 'Saison Épicée',
                'brewery' => 'Brasserie Saison',
                'style' => 'Saison',
                'color' => 'golden',
                'abv_x10' => 60,
                'ibu' => 30,
                'description' => 'Une saison rafraîchissante aux épices subtiles.',
                'is_on_tap' => false,
                'is_available' => true,
                'price' => 600,
                'tags' => ['epice', 'acidule'],
            ],
            [
                'name' => 'Pale Ale Tropicale',
                'brewery' => 'Brasserie Moderne',
                'style' => 'Pale Ale',
                'color' => 'golden',
                'abv_x10' => 50,
                'ibu' => 35,
                'description' => 'Une pale ale aux saveurs de fruits exotiques.',
                'is_on_tap' => true,
                'is_available' => true,
                'price' => 580,
                'tags' => ['fruits', 'agrumes'],
            ],
            [
                'name' => 'Double IPA',
                'brewery' => 'Brasserie Intense',
                'style' => 'Double IPA',
                'color' => 'amber',
                'abv_x10' => 85,
                'ibu' => 85,
                'description' => 'Une double IPA puissante aux arômes intenses de houblon.',
                'is_on_tap' => true,
                'is_available' => true,
                'price' => 750,
                'tags' => ['agrumes', 'fruits'],
            ],
            [
                'name' => 'Porter Fumé',
                'brewery' => 'Brasserie Fumée',
                'style' => 'Porter',
                'color' => 'brown',
                'abv_x10' => 60,
                'ibu' => 35,
                'description' => 'Un porter aux notes fumées et torréfiées.',
                'is_on_tap' => false,
                'is_available' => true,
                'price' => 680,
                'tags' => ['torrefie', 'malte'],
            ],
            [
                'name' => 'Wheat Beer',
                'brewery' => 'Brasserie Blé',
                'style' => 'Blanche',
                'color' => 'white',
                'abv_x10' => 50,
                'ibu' => 15,
                'description' => 'Une blanche rafraîchissante aux notes d\'agrumes.',
                'is_on_tap' => true,
                'is_available' => true,
                'price' => 520,
                'tags' => ['agrumes', 'acidule'],
            ],
            [
                'name' => 'Amber Ale',
                'brewery' => 'Brasserie Ambrée',
                'style' => 'Amber Ale',
                'color' => 'amber',
                'abv_x10' => 55,
                'ibu' => 30,
                'description' => 'Une amber ale équilibrée aux saveurs maltées.',
                'is_on_tap' => true,
                'is_available' => true,
                'price' => 570,
                'tags' => ['malte'],
            ],
            [
                'name' => 'Sour Framboise',
                'brewery' => 'Brasserie Acidulée',
                'style' => 'Sour',
                'color' => 'red',
                'abv_x10' => 45,
                'ibu' => 10,
                'description' => 'Une sour fruitée aux framboises, acidulée et rafraîchissante.',
                'is_on_tap' => false,
                'is_available' => true,
                'price' => 620,
                'tags' => ['fruits', 'acidule'],
            ],
            [
                'name' => 'Red IPA',
                'brewery' => 'Brasserie Rouge',
                'style' => 'Red IPA',
                'color' => 'red',
                'abv_x10' => 70,
                'ibu' => 60,
                'description' => 'Une red IPA aux arômes de fruits rouges et d\'épices.',
                'is_on_tap' => true,
                'is_available' => true,
                'price' => 690,
                'tags' => ['fruits', 'epice'],
            ],
            [
                'name' => 'Brown Ale',
                'brewery' => 'Brasserie Brune',
                'style' => 'Brown Ale',
                'color' => 'brown',
                'abv_x10' => 55,
                'ibu' => 25,
                'description' => 'Une brown ale aux notes de caramel et de noix.',
                'is_on_tap' => false,
                'is_available' => true,
                'price' => 590,
                'tags' => ['malte', 'torrefie'],
            ],
            [
                'name' => 'Gose Citron',
                'brewery' => 'Brasserie Salée',
                'style' => 'Gose',
                'color' => 'blonde',
                'abv_x10' => 45,
                'ibu' => 12,
                'description' => 'Une gose salée aux agrumes, légèrement acidulée.',
                'is_on_tap' => true,
                'is_available' => true,
                'price' => 560,
                'tags' => ['agrumes', 'acidule'],
            ],
            [
                'name' => 'Belgian Tripel',
                'brewery' => 'Brasserie Belge',
                'style' => 'Tripel',
                'color' => 'golden',
                'abv_x10' => 90,
                'ibu' => 30,
                'description' => 'Une tripel belge aux notes épicées et fruitées.',
                'is_on_tap' => false,
                'is_available' => true,
                'price' => 720,
                'tags' => ['epice', 'fruits'],
            ],
            [
                'name' => 'Hazy IPA',
                'brewery' => 'Brasserie Nuageuse',
                'style' => 'Hazy IPA',
                'color' => 'golden',
                'abv_x10' => 65,
                'ibu' => 50,
                'description' => 'Une hazy IPA trouble aux arômes de fruits tropicaux.',
                'is_on_tap' => true,
                'is_available' => true,
                'price' => 680,
                'tags' => ['fruits', 'agrumes'],
            ],
            [
                'name' => 'Imperial Stout',
                'brewery' => 'Brasserie Impériale',
                'style' => 'Imperial Stout',
                'color' => 'black',
                'abv_x10' => 100,
                'ibu' => 70,
                'description' => 'Une imperial stout puissante aux notes de café et chocolat.',
                'is_on_tap' => false,
                'is_available' => true,
                'price' => 850,
                'tags' => ['torrefie', 'malte'],
            ],
            [
                'name' => 'Berliner Weisse',
                'brewery' => 'Brasserie Berlin',
                'style' => 'Berliner Weisse',
                'color' => 'white',
                'abv_x10' => 35,
                'ibu' => 8,
                'description' => 'Une berliner weisse légère et acidulée.',
                'is_on_tap' => true,
                'is_available' => true,
                'price' => 540,
                'tags' => ['acidule'],
            ],
            [
                'name' => 'ESB',
                'brewery' => 'Brasserie Anglaise',
                'style' => 'ESB',
                'color' => 'amber',
                'abv_x10' => 55,
                'ibu' => 35,
                'description' => 'Une ESB équilibrée aux saveurs maltées et houblonnées.',
                'is_on_tap' => false,
                'is_available' => true,
                'price' => 600,
                'tags' => ['malte'],
            ],
            [
                'name' => 'Fruited Sour',
                'brewery' => 'Brasserie Fruitée',
                'style' => 'Sour',
                'color' => 'red',
                'abv_x10' => 50,
                'ibu' => 15,
                'description' => 'Une sour aux fruits de la passion, acidulée et fruitée.',
                'is_on_tap' => true,
                'is_available' => true,
                'price' => 640,
                'tags' => ['fruits', 'acidule', 'agrumes'],
            ],
            [
                'name' => 'Coffee Stout',
                'brewery' => 'Brasserie Café',
                'style' => 'Stout',
                'color' => 'black',
                'abv_x10' => 60,
                'ibu' => 45,
                'description' => 'Une stout au café, aux notes torréfiées intenses.',
                'is_on_tap' => false,
                'is_available' => true,
                'price' => 710,
                'tags' => ['torrefie'],
            ],
        ];

        foreach ($beers as $beerData) {
            $tags = $beerData['tags'];
            unset($beerData['tags']);

            $beer = Beer::create(array_merge($beerData, ['bar_id' => $bar->id]));

            $tagIds = collect($tags)->map(function ($slug) use ($tags) {
                return Tag::where('slug', $slug)->first()?->id;
            })->filter()->toArray();

            $beer->tags()->attach($tagIds);
        }
    }
}


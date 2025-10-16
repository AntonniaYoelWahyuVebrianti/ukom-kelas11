<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        User::updateOrCreate([
            'email' => 'admin@kwave.test',
        ], [
            'name' => 'K-Wave Admin',
            'password' => Hash::make('admin12345'),
            'is_admin' => true,
            'email_verified_at' => now(),
        ]);

        $categories = collect([
            ['name' => 'Lightstick', 'description' => 'Collectible lightsticks for every fandom.', 'image_url' => 'https://images.unsplash.com/photo-1514846326710-096e4a8035e1?auto=format&fit=crop&w=800&q=80'],
            ['name' => 'Merch', 'description' => 'Apparel and accessories inspired by K-Wave artists.', 'image_url' => 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?auto=format&fit=crop&w=800&q=80'],
            ['name' => 'Album', 'description' => 'Albums dan mini album eksklusif.', 'image_url' => 'https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?auto=format&fit=crop&w=800&q=80'],
        ])->map(function (array $payload) {
            return Category::updateOrCreate(
                ['slug' => Str::slug($payload['name'])],
                [
                    'name' => $payload['name'],
                    'description' => $payload['description'],
                    'image_url' => $payload['image_url'],
                ]
            );
        })->keyBy('name');

        $products = [
            [
                'name' => 'Limited Edition Lightstick',
                'slug' => 'limited-edition-lightstick',
                'sku' => 'KW-LS-001',
                'category' => 'Lightstick',
                'price' => 4500000,
                'stock' => 25,
                'description' => 'Official K-Wave lightstick with dynamic RGB effects for concert nights.',
                'image_url' => 'https://images.unsplash.com/photo-1582719471265-b95380395df3?auto=format&fit=crop&w=800&q=80',
            ],
            [
                'name' => 'Tour Hoodie 2025',
                'slug' => 'tour-hoodie-2025',
                'sku' => 'KW-HD-2025',
                'category' => 'Merch',
                'price' => 6500000,
                'stock' => 40,
                'description' => 'Oversized hoodie featuring the 2025 world tour lineup and premium fleece.',
                'image_url' => 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?auto=format&fit=crop&w=800&q=80',
            ],
            [
                'name' => 'Signed Mini Album',
                'slug' => 'signed-mini-album',
                'sku' => 'KW-ALB-010',
                'category' => 'Album',
                'price' => 5500000,
                'stock' => 15,
                'description' => 'Mini album signed by all members, includes exclusive photocard set.',
                'image_url' => 'https://images.unsplash.com/photo-1517638851339-4aa32003c11a?auto=format&fit=crop&w=800&q=80',
            ],
        ];

        foreach ($products as $payload) {
            $category = $categories->get($payload['category']);

            Product::updateOrCreate(
                ['slug' => $payload['slug']],
                array_merge($payload, [
                    'category_id' => $category?->id,
                ])
            );
        }
    }
}

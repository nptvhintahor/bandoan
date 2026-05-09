<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FoodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
{
    \App\Models\Food::create([
        'name' => 'Burger',
        'description' => 'Ngon tuyệt',
        'price' => 50000,
        'image' => '/storage/foods/sample.jpg'
    ]);
}
}

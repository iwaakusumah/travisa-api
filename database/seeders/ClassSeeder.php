<?php

namespace Database\Seeders;

use App\Models\ClassRoom;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['name' => 'X TKJ 1'],
            ['name' => 'X TKJ 2'],
            ['name' => 'X TKJ 3'],

            ['name' => 'XI TKJ 1'],
            ['name' => 'XI TKJ 2'],
            ['name' => 'XI TKJ 3'],
            
            ['name' => 'X TKR 1'],
            ['name' => 'X TKR 2'],
            ['name' => 'X TKR 3'],
            
            ['name' => 'XI TKR 1'],
            ['name' => 'XI TKR 2'],
            ['name' => 'XI TKR 3'],
            
            ['name' => 'X AP 1'],
            ['name' => 'X AP 2'],
            
            ['name' => 'XI AP 1'],
            ['name' => 'XI AP 2'],
            
            ['name' => 'X AK 1'],
            ['name' => 'X AK 2'],
            
            ['name' => 'XI AK 1'],
            ['name' => 'XI AK 2'],
        ];

        foreach ($data as $item) {
            ClassRoom::create([
                'name' => $item['name'],
            ]);
        }
    }
}

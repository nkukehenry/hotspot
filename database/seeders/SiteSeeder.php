<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Site;

class SiteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Site::create([
            'name' => 'Downtown',
            'address' => 'Downtown Area',
            'status' => 'active'
        ]);
        
        Site::create([
            'name' => 'Uptown',
            'address' => 'Uptown Area',
            'status' => 'active'
        ]);
    }
}

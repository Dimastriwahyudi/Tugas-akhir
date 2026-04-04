<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProdukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Produk::insert([
            ['nama' => 'Keripik Tempe', 'harga_jual' => 5000, 'harga_modal' => 3000, 'satuan' => 'bungkus', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Keripik Singkong', 'harga_jual' => 4000, 'harga_modal' => 2500, 'satuan' => 'bungkus', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Peyek Kacang', 'harga_jual' => 6000, 'harga_modal' => 3500, 'satuan' => 'bungkus', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}

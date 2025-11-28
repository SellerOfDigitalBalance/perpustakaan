<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DataBuku>
 */
class DataBukuFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'judul_buku' => $this->faker->sentence,
            'penulis_buku' => $this->faker->name,
            'penerbit_buku' => $this->faker->company,
            'tahun_terbit' => $this->faker->year,
            'categories_id' => \App\Models\Category::inRandomOrder()->first()->id,
            'ISBN' => $this->faker->isbn13,
            'jumlah_halaman' => $this->faker->numberBetween(100, 500),
            'deskripsi_singkat' => $this->faker->paragraph,
        ];
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BookSeeder extends Seeder
{
    protected int $booksTotal = 1000000;
    protected int $booksChunkSize = 5000;

    public function run(): void
    {
        $now = now();
        $this->command?->info("Seeding {$this->booksTotal} books...");

        for ($i = 0; $i < $this->booksTotal; $i += $this->booksChunkSize) {
            $batch = [];
            $limit = min($this->booksChunkSize, $this->booksTotal - $i);

            for ($j = 0; $j < $limit; $j++) {
                $index = $i + $j + 1;
                $copies = random_int(1, 20);

                $batch[] = [
                    'title' => 'Book #' . $index . ' ' . Str::title(Str::random(10)),
                    'isbn' => null,
                    'total_copies' => $copies,
                    'available_copies' => $copies,
                    'published_year' => random_int(1950, 2025),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            DB::table('books')->insert($batch);

            $this->command?->info("Inserted books: " . ($i + $limit) . " / {$this->booksTotal}");
        }
    }
}

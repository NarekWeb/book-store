<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AuthorSeeder extends Seeder
{
    protected int $authorsTotal = 50000;
    protected int $authorsChunkSize = 5000;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();
        $this->command?->info("Seeding {$this->authorsTotal} authors...");

        for ($i = 0; $i < $this->authorsTotal; $i += $this->authorsChunkSize) {
            $batch = [];
            $limit = min($this->authorsChunkSize, $this->authorsTotal - $i);

            for ($j = 0; $j < $limit; $j++) {
                $index = $i + $j + 1;

                $batch[] = [
                    'name'       => 'Author #' . $index . ' ' . Str::title(Str::random(8)),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            DB::table('authors')->insert($batch);

            $this->command?->info("Inserted authors: " . ($i + $limit) . " / {$this->authorsTotal}");
        }
    }
}

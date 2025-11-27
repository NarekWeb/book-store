<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FastBooksSeeder extends Seeder
{
    protected int $booksTotal = 50_000;
    protected int $booksChunkSize = 5_000;

    protected int $authorsTotal = 50_000;
    protected int $authorsChunkSize = 5_000;

    public function run(): void
    {
        DB::connection()->disableQueryLog();

        $this->seedAuthors();
        $this->seedBooks();
        $this->seedAuthorBookRelations();
    }

    protected function seedAuthors(): void
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

    protected function seedBooks(): void
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
                    'title'            => 'Book #' . $index . ' ' . Str::title(Str::random(10)),
                    'isbn'             => null, // или сгенерируй, если нужно
                    'total_copies'     => $copies,
                    'available_copies' => $copies,
                    'published_year'   => random_int(1950, 2025),
                    'created_at'       => $now,
                    'updated_at'       => $now,
                ];
            }

            DB::table('books')->insert($batch);

            $this->command?->info("Inserted books: " . ($i + $limit) . " / {$this->booksTotal}");
        }
    }

    protected function seedAuthorBookRelations(): void
    {
        $this->command?->info('Seeding author_book relations...');

        $authorIds = DB::table('authors')->pluck('id')->all();

        DB::table('books')
            ->orderBy('id')
            ->chunkById(1000, function ($books) use ($authorIds) {
                $pivotBatch = [];

                foreach ($books as $book) {
                    $perBook = random_int(1, 3);
                    $randKeys = (array) array_rand($authorIds, $perBook);

                    foreach ($randKeys as $key) {
                        $pivotBatch[] = [
                            'book_id'    => $book->id,
                            'author_id'  => $authorIds[$key],
                        ];
                    }
                }

                if (! empty($pivotBatch)) {
                    DB::table('author_book')->insert($pivotBatch);
                }

                $this->command?->info('Processed books up to ID ' . $books->last()->id);
            });

        $this->command?->info('author_book seeding finished.');
    }
}

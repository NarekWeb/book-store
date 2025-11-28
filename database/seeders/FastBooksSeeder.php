<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FastBooksSeeder extends Seeder
{
    public function run(): void
    {
        DB::connection()->disableQueryLog();

        $this->call(class: AuthorSeeder::class);
        $this->call(class: BookSeeder::class);
        $this->seedAuthorBookRelations();
    }

    protected function seedAuthorBookRelations(): void
    {
        $this->command?->info('Seeding author_book relations...');

        $authorIds = DB::table('authors')->pluck('id')->all();

        DB::table('books')
            ->orderBy('id')
            ->chunkById(2000, function ($books) use ($authorIds) {
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

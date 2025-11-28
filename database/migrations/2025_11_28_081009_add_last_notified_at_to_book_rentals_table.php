<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('book_rentals', function (Blueprint $table) {
            if (! Schema::hasColumn('book_rentals', 'last_notified_at')) {
                $table->dateTime('last_notified_at')->nullable()->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('book_rentals', function (Blueprint $table) {
            $table->dropColumn('last_notified_at');
        });
    }
};

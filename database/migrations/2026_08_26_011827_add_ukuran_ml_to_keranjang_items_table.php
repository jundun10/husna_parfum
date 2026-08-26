<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $database = DB::getDatabaseName();
        $table = 'keranjang_items';

        /*
        |--------------------------------------------------------------------------
        | 1. Pastikan index user_id terpisah ada
        |--------------------------------------------------------------------------
        */
        $userIndexExists = DB::table('information_schema.statistics')
            ->where('table_schema', $database)
            ->where('table_name', $table)
            ->where('column_name', 'user_id')
            ->where('seq_in_index', 1)
            ->where(
                'index_name',
                '!=',
                'keranjang_items_user_id_parfum_id_unique'
            )
            ->exists();

        if (!$userIndexExists) {
            Schema::table($table, function (Blueprint $table) {
                $table->index(
                    'user_id',
                    'keranjang_items_user_id_index'
                );
            });
        }

        /*
        |--------------------------------------------------------------------------
        | 2. Pastikan index parfum_id terpisah ada
        |--------------------------------------------------------------------------
        */
        $parfumIndexExists = DB::table('information_schema.statistics')
            ->where('table_schema', $database)
            ->where('table_name', $table)
            ->where('column_name', 'parfum_id')
            ->where('seq_in_index', 1)
            ->where(
                'index_name',
                '!=',
                'keranjang_items_user_id_parfum_id_unique'
            )
            ->exists();

        if (!$parfumIndexExists) {
            Schema::table($table, function (Blueprint $table) {
                $table->index(
                    'parfum_id',
                    'keranjang_items_parfum_id_index'
                );
            });
        }

        /*
        |--------------------------------------------------------------------------
        | 3. Hapus unique lama
        |--------------------------------------------------------------------------
        */
        $oldUniqueExists = DB::table('information_schema.statistics')
            ->where('table_schema', $database)
            ->where('table_name', $table)
            ->where(
                'index_name',
                'keranjang_items_user_id_parfum_id_unique'
            )
            ->exists();

        if ($oldUniqueExists) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropUnique(
                    'keranjang_items_user_id_parfum_id_unique'
                );
            });
        }

        /*
        |--------------------------------------------------------------------------
        | 4. Buat unique baru
        |--------------------------------------------------------------------------
        */
        $newUniqueExists = DB::table('information_schema.statistics')
            ->where('table_schema', $database)
            ->where('table_name', $table)
            ->where(
                'index_name',
                'keranjang_items_user_id_parfum_id_ukuran_ml_unique'
            )
            ->exists();

        if (!$newUniqueExists) {
            Schema::table($table, function (Blueprint $table) {
                $table->unique(
                    [
                        'user_id',
                        'parfum_id',
                        'ukuran_ml',
                    ],
                    'keranjang_items_user_id_parfum_id_ukuran_ml_unique'
                );
            });
        }
    }

    public function down(): void
    {
        $database = DB::getDatabaseName();
        $table = 'keranjang_items';

        /*
        |--------------------------------------------------------------------------
        | Hapus unique baru
        |--------------------------------------------------------------------------
        */
        $newUniqueExists = DB::table('information_schema.statistics')
            ->where('table_schema', $database)
            ->where('table_name', $table)
            ->where(
                'index_name',
                'keranjang_items_user_id_parfum_id_ukuran_ml_unique'
            )
            ->exists();

        if ($newUniqueExists) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropUnique(
                    'keranjang_items_user_id_parfum_id_ukuran_ml_unique'
                );
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Kembalikan unique lama
        |--------------------------------------------------------------------------
        */
        $oldUniqueExists = DB::table('information_schema.statistics')
            ->where('table_schema', $database)
            ->where('table_name', $table)
            ->where(
                'index_name',
                'keranjang_items_user_id_parfum_id_unique'
            )
            ->exists();

        if (!$oldUniqueExists) {
            Schema::table($table, function (Blueprint $table) {
                $table->unique(
                    [
                        'user_id',
                        'parfum_id',
                    ],
                    'keranjang_items_user_id_parfum_id_unique'
                );
            });
        }

        $columnExists = DB::table('information_schema.columns')
            ->where('table_schema', $database)
            ->where('table_name', $table)
            ->where('column_name', 'ukuran_ml')
            ->exists();

        if ($columnExists) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropColumn('ukuran_ml');
            });
        }
    }
};
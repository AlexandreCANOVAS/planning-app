<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MarkMigrationCompleted extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('migrations')->insert([
            'migration' => '2025_06_28_174306_update_document_categories_table',
            'batch' => DB::table('migrations')->max('batch') + 1,
        ]);
    }
}

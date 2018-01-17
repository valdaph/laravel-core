<?php

namespace Valda\Database;

use Carbon\Carbon;
use Illuminate\Database\Seeder as BaseSeeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

abstract class Seeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!isset($this->table)) {
            throw new \Exception('No table specified.');
        }

        if ((!isset($this->rows) && !method_exists($this, 'rows'))) {
            throw new \Exception('No rows specified.');
        }

        $rows = isset($this->rows) ? $this->rows : $this->rows();

        foreach ($rows as &$row) {
            if (Schema::hasColumn($this->table, 'created_at') &&
                Schema::getColumnType($this->table, 'created_at') === 'datetime') {
                $row['created_at'] = Carbon::now()->toDateTimeString();
            }

            if (Schema::hasColumn($this->table, 'updated_at') &&
                Schema::getColumnType($this->table, 'updated_at') === 'datetime') {
                $row['updated_at'] = Carbon::now()->toDateTimeString();
            }
        }

        DB::table($this->table)->insert($rows);
    }
}

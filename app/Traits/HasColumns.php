<?php

namespace Valda\Traits;

use Illuminate\Support\Facades\Schema;

trait HasColumns
{
    /**
     * Get the columns of the model's table.
     *
     * @param  boolean  $timestamps
     * @return array
     */
    public function getTableColumns($timestamps = true)
    {
        $columns = Schema::getColumnListing($this->getTable());
        $columns = array_filter($columns, function ($column) {
            return !in_array($column, $this->hidden);
        });

        if ($timestamps) {
            return $columns;
        }

        return array_filter($columns, function ($column) {
            return !in_array($column, [
                'created_at',
                'updated_at',
                'deleted_at',
            ]);
        });
    }

    /**
     * Get the columns of the model's table in a readable format.
     *
     * @param  boolean  $timestamps
     * @return array
     */
    public function getReadableColumns($timestamps = true)
    {
        return collect($this->getTableColumns($timestamps))
            ->mapWithKeys(function ($column) {
                return [
                    $column => str_replace('_', ' ', $column),
                ];
            })
            ->all();
    }
}
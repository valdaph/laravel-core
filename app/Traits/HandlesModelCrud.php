<?php

namespace Valda\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Laravel\Scout\Searchable;
use ScoutElastic\Searchable as ElasticSearchable;

trait HandlesModelCrud
{
    /**
     * The resource to be used.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;
    
    /**
     * The columns exluded from queries.
     *
     * @var array
     */
    protected $excludeFromQuery = [
        'id',
        'password',
        'remember_token',
        'deleted_at',
    ];

    /**
     * Get the appropriate builder for the querying the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $model
     * @return $this
     */
    protected function getBuilder(Request $request, &$model)
    {
        if ($this->isSearching($request)) {
            $model = $this->getSearchableBuilder($request);
        } else {
            $model = $model instanceof Builder ? $model : $model->query();
        }

        return $this;
    }

    /**
     * Get the searcahble builder of the resource.
     *
     * @param  Request  $request
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function getSearchableBuilder(Request $request)
    {
        $model = get_class($this->model)::search($request->query('q'));
        $params = array_only($request->all(), $this->getSearchableAttributes());

        foreach ($params as $key => $value) {
            $model->where($key, $value);
        }

        return $this->model->newQuery()->whereIn('id', $model->take(10000)->get()->pluck('id')->toArray());
    }

    /**
     * Apply where conditions to model.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $model
     * @param  array  $except
     * @return array
     */
    protected function applyWhere(Request $request, &$model)
    {
        $where = array();
        
        $tableName = $this->model->getTable();
        $strict = filter_var($request->query('strict', false), FILTER_VALIDATE_BOOLEAN);

        $attributes = $this->isSearching($request)
            ? array_values(array_diff($this->getModelAttributes(), $this->getSearchableAttributes()))
            : $this->getModelAttributes();

        $params = array_only($request->all(), $attributes);

        foreach ($params as $key => $value) {
            $tableKey = $tableName . '.' . $key;

            switch (Schema::getColumnType($tableName, $key)) {
                case 'boolean':
                    $where[] = [$tableKey, '=', filter_var($value, FILTER_VALIDATE_BOOLEAN)];
                    break;

                case 'integer':
                    $value = $value === 'null' ? null : filter_var($value, FILTER_VALIDATE_INT);
                    $where[] = [$tableKey, '=', $value];
                    break;

                case 'string':
                case 'text':
                    $where[] = $strict ? [$tableKey, '=', $value] : [$tableKey, 'like', '%' . $value . '%'];
                    break;
            }
        }

        if (count($where) > 0) {
            $model->where($where);
        }

        return $this;
    }

    /**
     * Check if the request is searching for a resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return boolean
     */
    protected function isSearching(Request $request)
    {
        $modelTraits = class_uses(get_class($this->model));

        $isSearchable = in_array(Searchable::class, $modelTraits)
            || in_array(ElasticSearchable::class, $modelTraits);
        
        return $request->filled('q') && $isSearchable;
    }

    /**
     * Get all of the model's attributes.
     *
     * @return array
     */
    protected function getModelAttributes()
    {
        return array_except(Schema::getColumnListing($this->model->getTable()), $this->excludeFromQuery);
    }

    /**
     * Get all of the model's searchable attributes.
     *
     * @return array
     */
    protected function getSearchableAttributes()
    {
        return array_except(array_keys($this->model->getMapping()['properties']), $this->excludeFromQuery);
    }
}

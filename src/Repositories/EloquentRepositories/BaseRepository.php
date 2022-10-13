<?php

namespace Xkairo\CacheRepositoryLaravel\Repositories\EloquentRepositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Xkairo\CacheRepositoryLaravel\Repositories\BaseRepositoryInterface;

abstract class BaseRepository implements BaseRepositoryInterface
{
    protected $model;
    protected $relations;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function all(array $sort_by = ['id', 'DESC'], array $filters = [], bool $withTrashed = false)
    {
        $query = $this->model;

        if ($withTrashed) $this->withTrashed($query);

        if ($this->relations && count($this->relations) > 0) {
            $query = $query->with($this->relations);
        }

        $this->sortAndFilter($query, $sort_by, $filters);

        return $query->get();
    }

    public function paginate(int $quantity, array $sort_by = ['id', 'DESC'], array $filters = [], bool $withTrashed = false)
    {
        $query = $this->model;

        if ($withTrashed) $this->withTrashed($query);

        if ($this->relations && count($this->relations) > 0) {
            $query = $query->with($this->relations);
        }

        $this->sortAndFilter($query, $sort_by, $filters);

        return $query->paginate($quantity);
    }

    public function getById(int $id, bool $withTrashed = false)
    {
        $query = $this->model;

        if ($withTrashed) $this->withTrashed($query);

        $instance = $query->where('id', $id)->first();

        if ($instance === null) {
            throw new ModelNotFoundException();
        }

        if ($this->relations && count($this->relations) > 0) {
            $instance->load($this->relations);
        }

        return $instance;
    }

    public function create(array $data)
    {
        $this->model->fill($data);

        $this->model->save();

        $this->model->refresh();

        return $this->model;
    }

    public function update(array $data, Model|int $model)
    {
        $instance = $this->checkInstanceOrId($model);

        return $instance->update($data);
    }

    public function delete(Model|int $model)
    {
        $instance = $this->checkInstanceOrId($model);

        return $instance->delete();
    }

    public function forceDelete(Model|int $model)
    {
        $instance = $this->checkInstanceOrId($model);

        return  $instance->forceDelete();
    }

    public function load(Model|int $model, array|string $relations)
    {
        $instance = $this->checkInstanceOrId($model);

        return $instance->load($relations);
    }

    public function loadWithTrashed(Model|int $model, array|string $relations)
    {
        $instance = $this->checkInstanceOrId($model);

        $arrayRelations = is_string($relations) ? [$relations] : $relations;

        $relationsTrashed = array();

        foreach ($arrayRelations as $relation) {
            $relationsTrashed[$relation] = fn ($q) => $q->withTrashed();
        }
    }

    protected function sortAndFilter(&$query, array $sort_by = ['id', 'DESC'], array $filters = [])
    {
        $query = $query->orderBy(...$sort_by);

        foreach ($filters as $filter) {
            $query = $query->where(...$filter);
        }

        return $query;
    }

    //TODO Change the name of this method (convertInstanceOrIdInInstance)
    protected function checkInstanceOrId(Model|int $model)
    {
        if (gettype($model) === 'integer') {
            $instance = $this->getById($model);
        } else {
            $instance = $model;
        }

        return $instance;
    }

    protected function withTrashed($query)
    {
        if (function_exists('withTrashed')) {
            $query = $query->withTrashed();
        }
    }
}

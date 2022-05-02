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

    public function all()
    {
        $query = $this->model;

        if ($this->relations && count($this->relations) > 0) {
            $query->with($this->relations);
        }

        return $query->get();
    }

    public function paginate(int $quantity)
    {
        $query = $this->model;

        if ($this->relations && count($this->relations) > 0) {
            $query->with($this->relations);
        }

        return $query->paginate($quantity);
    }

    public function getById(int $id)
    {
        $instance = $this->model->find($id);

        if ($instance === null) {
            throw new ModelNotFoundException();
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
        if (gettype($model) === 'integer') {
            $instance = $this->getById($model);
        } else {
            $instance = $model;
        }


        return $instance->update($data);
    }

    public function delete(Model|int $model)
    {
        if (gettype($model) === 'integer') {
            $instance = $this->getById($model);
        } else {
            $instance = $model;
        }

        return $instance->delete();
    }

    public function forceDelete(Model|int $model)
    {
        if (gettype($model) === 'integer') {
            $instance = $this->getById($model);
        } else {
            $instance = $model;
        }

        return  $instance->forceDelete();
    }
}

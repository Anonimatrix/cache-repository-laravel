<?php

namespace Xkairo\CacheRepositoryLaravel\Cache;

use Illuminate\Http\Request;
use Illuminate\Cache\Repository as Cache;
use Illuminate\Database\Eloquent\Model;
use Xkairo\CacheRepositoryLaravel\Repositories\BaseRepositoryInterface;

abstract class BaseCache
{
    protected $repository;
    protected $cache;
    protected $key;
    protected $request;
    const TTL = 86400;

    public function __construct(BaseRepositoryInterface $repository, Cache $cache, Request $request, string $key)
    {
        $this->request = $request;
        $this->cache = $cache;
        $this->repository = $repository;
        $this->key = $key;
    }

    public function all()
    {
        return $this->cache->tags([$this->key . 's'])->remember($this->key . 's', self::TTL, function () {
            return $this->repository->all();
        });
    }

    public function paginate(int $quantity)
    {
        return $this->cache->tags([$this->key . 's'])->remember($this->key . "s-page-{$this->request->page}", self::TTL, function () use ($quantity) {
            return $this->repository->paginate($quantity);
        });
    }

    public function getById(int $id)
    {
        return $this->cache->tags([$this->key . "-$id"])->remember($this->key . "-$id-get-by-id", self::TTL, function () use ($id) {
            return $this->repository->getById($id);
        });
    }

    public function create(array $data)
    {
        $this->cache->tags([$this->key . 's'])->flush();
        return $this->repository->create($data);
    }

    public function update(array $data, Model|int $model)
    {
        $model_id = gettype($model) === 'integer' ? $model : $model->id;

        $this->cache->tags([$this->key . "-$model_id"])->flush();
        return $this->repository->update($data, $model);
    }

    public function delete(Model|int $model)
    {
        $model_id = gettype($model) === 'integer' ? $model : $model->id;

        $this->cache->tags([$this->key])->forget($this->key . "-$model_id");
        return $this->repository->delete($model);
    }

    public function forceDelete(Model|int $model)
    {
        $model_id = gettype($model) === 'integer' ? $model : $model->id;

        $this->cache->tags([$this->key])->forget($this->key . "-$model_id");
        return $this->repository->forceDelete($model);
    }
}

<?php

namespace Xkairo\CacheRepositoryLaravel\Cache;

use Illuminate\Http\Request;
use Illuminate\Cache\Repository as Cache;
use Illuminate\Database\Eloquent\Model;
use Xkairo\CacheRepositoryLaravel\Repositories\BaseRepositoryInterface;

abstract class BaseCache implements BaseRepositoryInterface
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

    public function all(array $sort_by = ['id', 'DESC'], array $filters = [], bool $withTrashed = false)
    {
        return $this->cache->tags([$this->key . 's'])->remember($this->getRememberString(true, null, null, $sort_by = $sort_by, $filters = $filters, $withTrashed), self::TTL, function () use ($sort_by, $filters, $withTrashed) {
            return $this->repository->all($sort_by, $filters, $withTrashed);
        });
    }

    public function paginate(int $quantity, array $sort_by = ['id', 'DESC'], array $filters = [], bool $withTrashed = false)
    {
        return $this->cache->tags([$this->key . 's'])->remember($this->getRememberString(true, $this->request->page, null, $sort_by, $filters, $withTrashed), self::TTL, function () use ($quantity, $sort_by, $filters, $withTrashed) {
            return $this->repository->paginate($quantity, $sort_by, $filters, $withTrashed);
        });
    }

    public function getById(int $id, bool $withTrashed = false)
    {
        return $this->cache->tags([$this->key . "-$id"])->remember($this->getRememberString(false, null, "-$id-get-by-id", null, [], $withTrashed), self::TTL, function () use ($id, $withTrashed) {
            return $this->repository->getById($id, $withTrashed);
        });
    }

    public function create(array $data)
    {
        $this->cache->tags([$this->key . 's'])->flush();
        return $this->repository->create($data);
    }

    public function update(array $data, Model|int $model)
    {
        $model_id = $this->checkInstanceOrId($model);

        $this->cache->tags([$this->key . "-$model_id"])->flush();
        return $this->repository->update($data, $model);
    }

    public function delete(Model|int $model)
    {
        $model_id = $this->checkInstanceOrId($model);

        $this->cache->tags([$this->key])->forget($this->key . "-$model_id");
        return $this->repository->delete($model);
    }

    public function forceDelete(Model|int $model)
    {
        $model_id = $this->checkInstanceOrId($model);

        $this->cache->tags([$this->key])->forget($this->key . "-$model_id");
        return $this->repository->forceDelete($model);
    }

    public function load(Model|int $model, array|string $relations)
    {
        return $this->repository->load($model, $relations);
    }

    public function loadWithTrashed(Model|int $model, array|string $relations)
    {
        return $this->repository->loadWithTrashed($model, $relations);
    }

    protected function getRememberString($plural = false, $page = null, $extra = null, $sort_by = null, $filters = [], $withTrashed = false)
    {
        $filtersString = "";
        foreach ($filters as $filter) {
            $filtersString .= implode("", $filter);
        }
        return
            $this->key
            . ($plural ? "s" : "")
            . ($page ? "-page-{$page}" : "")
            . ($filters ? "-filters-" . $filtersString : "")
            . ($sort_by ? "-sort_by-" . implode($sort_by) : "")
            . ($extra ? "-$extra" : "")
            . ($withTrashed ? 'with-trashed' : '');
    }

    protected function checkInstanceOrId(Model|int $model): int
    {
        return gettype($model) === 'integer' ? $model : $model->id;
    }
}

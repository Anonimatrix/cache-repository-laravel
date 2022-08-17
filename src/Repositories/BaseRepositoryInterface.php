<?php

namespace Xkairo\CacheRepositoryLaravel\Repositories;

use Illuminate\Database\Eloquent\Model;

interface BaseRepositoryInterface
{
    public function all(array $sort_by = ['id', 'DESC'], array $filters = [], bool $withTrashed = false);

    public function paginate(int $quantity, array $sort_by = ['id', 'DESC'], array $filters = [], bool $withTrashed = false);

    public function getById(int $id, bool $withTrashed = false);

    public function create(array $data);

    public function update(array $data, int $id);

    public function delete(int $id);

    public function forceDelete(int $id);

    public function load(Model|int $model, array|string $relations);

    public function loadWithTrashed(Model|int $model, array|string $relations);
}

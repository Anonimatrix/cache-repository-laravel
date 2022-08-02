<?php

namespace Xkairo\CacheRepositoryLaravel\Repositories;

interface BaseRepositoryInterface
{
    public function all(array $sort_by = ['id', 'DESC'], array $filters = []);

    public function paginate(int $quantity, array $sort_by = ['id', 'DESC'], array $filters = []);

    public function getById(int $id);

    public function create(array $data);

    public function update(array $data, int $id);

    public function delete(int $id);

    public function forceDelete(int $id);
}

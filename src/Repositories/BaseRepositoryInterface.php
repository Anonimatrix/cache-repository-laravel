<?php

namespace Xkairo\CacheRepositoryLaravel\Repositories;

interface BaseRepositoryInterface
{
    public function all();

    public function paginate(int $quantity);

    public function getById(int $id);

    public function create(array $data);

    public function update(array $data, int $id);

    public function delete(int $id);

    public function forceDelete(int $id);

    /**
     * Return elements sorted and filtered
     * 
     * @param array $sort_by
     * @param array $filters
     * @param array $options = [
     *      'paginate' => (int|null)
     * ]
     * 
     * @return Collection
     * 
     */
    public function sortedWithFilters(array $sort_by, array $filters, array $options);
}

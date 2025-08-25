<?php
namespace App\Models;

interface ModelInterface {
    /**
     * Create a new record
     *
     * @param array $data
     * @return mixed
     */
    public function create(array $data);

    /**
     * Find a record by ID
     *
     * @param int $id
     * @return array|null
     */
    public function findById(int $id): ?array;

    /**
     * Update a record by ID
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool;

    /**
     * Delete a record by ID
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;

    /**
     * Get all records
     *
     * @return array
     */
    public function getAll(): array;
}

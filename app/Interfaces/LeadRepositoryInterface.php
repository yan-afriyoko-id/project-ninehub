<?php

namespace App\Interfaces;
use App\Models\Lead;

interface LeadRepositoryInterface
{
    public function all(array $relations = []): iterable;
    public function create(array $data): Lead;
    public function getById($id, array $relations = []): ?Lead;
    public function update(Lead $Lead, array $data): Lead;
    public function delete($id): bool;
    public function findByContactId(int $ContactId): ?Lead;

}

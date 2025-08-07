<?php

namespace App\Interfaces;

use App\Models\Lead;
use Illuminate\Database\Eloquent\Collection;

interface LeadRepositoryInterface
{
    public function all(): Collection;
    public function find(int $id): ?Lead;
    public function findOrFail(int $id): Lead;
    public function create(array $data): Lead;
    public function update(int $id, array $data): Lead;
    public function delete(int $id): bool;
    public function paginate(array $filters = []): \Illuminate\Pagination\LengthAwarePaginator;
    public function getLeadsByContact(int $contactId): Collection;
    public function searchLeads(string $search): Collection;
    public function getLeadStatistics(): array;
}

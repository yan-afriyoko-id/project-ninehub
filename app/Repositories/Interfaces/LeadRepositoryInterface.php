<?php

namespace App\Repositories\Interfaces;

use App\Models\Lead;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface LeadRepositoryInterface
{
    /**
     * Get all leads.
     */
    public function all(): Collection;

    /**
     * Find lead by ID.
     */
    public function find(int $id): ?Lead;

    /**
     * Find lead by ID or throw exception.
     */
    public function findOrFail(int $id): Lead;

    /**
     * Create a new lead.
     */
    public function create(array $data): Lead;

    /**
     * Update an existing lead.
     */
    public function update(int $id, array $data): Lead;

    /**
     * Delete a lead.
     */
    public function delete(int $id): bool;

    /**
     * Get paginated leads with filters.
     */
    public function paginate(array $filters = []): LengthAwarePaginator;

    /**
     * Get leads by contact.
     */
    public function getLeadsByContact(int $contactId): Collection;

    /**
     * Search leads by title or description.
     */
    public function searchLeads(string $search): Collection;

    /**
     * Get lead statistics.
     */
    public function getLeadStatistics(): array;
}

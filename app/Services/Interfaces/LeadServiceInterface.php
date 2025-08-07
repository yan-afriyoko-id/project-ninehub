<?php

namespace App\Services\Interfaces;

use App\Models\Lead;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface LeadServiceInterface
{
    /**
     * Get all leads with optional filters.
     */
    public function getAllLeads(array $filters = []): LengthAwarePaginator;

    /**
     * Get lead by ID.
     */
    public function getLeadById(int $id): ?Lead;

    /**
     * Create a new lead.
     */
    public function createLead(array $data): Lead;

    /**
     * Update an existing lead.
     */
    public function updateLead(int $id, array $data): Lead;

    /**
     * Delete a lead.
     */
    public function deleteLead(int $id): bool;

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

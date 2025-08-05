<?php

namespace App\Services;

use App\Models\Lead;
use App\Repositories\Interfaces\LeadRepositoryInterface;
use App\Services\Interfaces\LeadServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class LeadService implements LeadServiceInterface
{
    private LeadRepositoryInterface $repository;

    public function __construct(LeadRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get all leads with optional filters.
     */
    public function getAllLeads(array $filters = []): LengthAwarePaginator
    {
        return $this->repository->paginate($filters);
    }

    /**
     * Get lead by ID.
     */
    public function getLeadById(int $id): ?Lead
    {
        return $this->repository->find($id);
    }

    /**
     * Create a new lead.
     */
    public function createLead(array $data): Lead
    {
        return $this->repository->create($data);
    }

    /**
     * Update an existing lead.
     */
    public function updateLead(int $id, array $data): Lead
    {
        return $this->repository->update($id, $data);
    }

    /**
     * Delete a lead.
     */
    public function deleteLead(int $id): bool
    {
        return $this->repository->delete($id);
    }

    /**
     * Get leads by contact.
     */
    public function getLeadsByContact(int $contactId): Collection
    {
        return $this->repository->getLeadsByContact($contactId);
    }

    /**
     * Search leads by title or description.
     */
    public function searchLeads(string $search): Collection
    {
        return $this->repository->searchLeads($search);
    }

    /**
     * Get lead statistics.
     */
    public function getLeadStatistics(): array
    {
        return $this->repository->getLeadStatistics();
    }
}

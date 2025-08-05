<?php

namespace App\Repositories;

use App\Models\Lead;
use App\Repositories\Interfaces\LeadRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class LeadRepository implements LeadRepositoryInterface
{
    private Lead $model;

    public function __construct(Lead $model)
    {
        $this->model = $model;
    }

    /**
     * Get all leads.
     */
    public function all(): Collection
    {
        return $this->model->with(['contact'])->get();
    }

    /**
     * Find lead by ID.
     */
    public function find(int $id): ?Lead
    {
        return $this->model->with(['contact'])->find($id);
    }

    /**
     * Find lead by ID or throw exception.
     */
    public function findOrFail(int $id): Lead
    {
        return $this->model->with(['contact'])->findOrFail($id);
    }

    /**
     * Create a new lead.
     */
    public function create(array $data): Lead
    {
        $lead = $this->model->create($data);
        return $lead->load(['contact']);
    }

    /**
     * Update an existing lead.
     */
    public function update(int $id, array $data): Lead
    {
        $lead = $this->findOrFail($id);
        $lead->update($data);
        return $lead->fresh(['contact']);
    }

    /**
     * Delete a lead.
     */
    public function delete(int $id): bool
    {
        $lead = $this->find($id);
        if ($lead) {
            return $lead->delete();
        }
        return false;
    }

    /**
     * Get paginated leads with filters.
     */
    public function paginate(array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->with(['contact']);

        // Apply filters
        if (isset($filters['search']) && !empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('email', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('phone', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('notes', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (isset($filters['contact_id']) && !empty($filters['contact_id'])) {
            $query->where('contact_id', $filters['contact_id']);
        }

        if (isset($filters['status']) && !empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $perPage = isset($filters['per_page']) ? (int) $filters['per_page'] : 15;
        return $query->paginate($perPage);
    }

    /**
     * Get leads by contact.
     */
    public function getLeadsByContact(int $contactId): Collection
    {
        return $this->model->with(['contact'])
            ->where('contact_id', $contactId)
            ->get();
    }

    /**
     * Search leads by name or email.
     */
    public function searchLeads(string $search): Collection
    {
        return $this->model->with(['contact'])
            ->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('phone', 'like', '%' . $search . '%')
                    ->orWhere('notes', 'like', '%' . $search . '%');
            })
            ->get();
    }

    /**
     * Get lead statistics.
     */
    public function getLeadStatistics(): array
    {
        return [
            'total_leads' => $this->model->count(),
            'leads_with_contacts' => $this->model->whereNotNull('contact_id')->count(),
            'leads_without_contacts' => $this->model->whereNull('contact_id')->count(),
            'recent_leads' => $this->model->latest()->take(5)->count(),
            'leads_by_status' => $this->model->selectRaw('status, count(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray(),
            'leads_with_potential_value' => $this->model->whereNotNull('potential_value')->count(),
            'leads_without_potential_value' => $this->model->whereNull('potential_value')->count(),
        ];
    }
}

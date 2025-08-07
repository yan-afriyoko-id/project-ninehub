<?php

namespace App\Repositories;

use App\Models\Contact;
use App\Repositories\Interfaces\ContactRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ContactRepository implements ContactRepositoryInterface
{
    private Contact $model;

    public function __construct(Contact $model)
    {
        $this->model = $model;
    }

    /**
     * Get all contacts.
     */
    public function all(): Collection
    {
        return $this->model->with(['company', 'leads'])->get();
    }

    /**
     * Find contact by ID.
     */
    public function find(int $id): ?Contact
    {
        return $this->model->with(['company', 'leads'])->find($id);
    }

    /**
     * Find contact by ID or throw exception.
     */
    public function findOrFail(int $id): Contact
    {
        return $this->model->with(['company', 'leads'])->findOrFail($id);
    }

    /**
     * Create a new contact.
     */
    public function create(array $data): Contact
    {
        $contact = $this->model->create($data);
        return $contact->load(['company', 'leads']);
    }

    /**
     * Update an existing contact.
     */
    public function update(int $id, array $data): Contact
    {
        $contact = $this->findOrFail($id);
        $contact->update($data);
        return $contact->fresh(['company', 'leads']);
    }

    /**
     * Delete a contact.
     */
    public function delete(int $id): bool
    {
        $contact = $this->find($id);
        if ($contact) {
            return $contact->delete();
        }
        return false;
    }

    /**
     * Get paginated contacts with filters.
     */
    public function paginate(array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->with(['company', 'leads']);

        // Apply filters
        if (isset($filters['search']) && !empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('first_name', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('last_name', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('email', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('phone', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (isset($filters['company_id']) && !empty($filters['company_id'])) {
            $query->where('company_id', $filters['company_id']);
        }

        $perPage = isset($filters['per_page']) ? (int) $filters['per_page'] : 15;
        return $query->paginate($perPage);
    }

    /**
     * Get contacts by company.
     */
    public function getContactsByCompany(int $companyId): Collection
    {
        return $this->model->with(['company', 'leads'])
            ->where('company_id', $companyId)
            ->get();
    }

    /**
     * Search contacts by name or email.
     */
    public function searchContacts(string $search): Collection
    {
        return $this->model->with(['company', 'leads'])
            ->where(function ($query) use ($search) {
                $query->where('first_name', 'like', '%' . $search . '%')
                    ->orWhere('last_name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('phone', 'like', '%' . $search . '%');
            })
            ->get();
    }

    /**
     * Get contact statistics.
     */
    public function getContactStatistics(): array
    {
        return [
            'total_contacts' => $this->model->count(),
            'contacts_with_companies' => $this->model->whereNotNull('company_id')->count(),
            'contacts_without_companies' => $this->model->whereNull('company_id')->count(),
            'recent_contacts' => $this->model->latest()->take(5)->count(),
            'contacts_with_job_titles' => $this->model->whereNotNull('job_title')->count(),
            'contacts_without_job_titles' => $this->model->whereNull('job_title')->count(),
        ];
    }
}

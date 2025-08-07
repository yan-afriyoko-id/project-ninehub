<?php

namespace App\Services;

use App\Models\Contact;
use App\Repositories\Interfaces\ContactRepositoryInterface;
use App\Services\Interfaces\ContactServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ContactService implements ContactServiceInterface
{
    private ContactRepositoryInterface $repository;

    public function __construct(ContactRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get all contacts with optional filters.
     */
    public function getAllContacts(array $filters = []): LengthAwarePaginator
    {
        return $this->repository->paginate($filters);
    }

    /**
     * Get contact by ID.
     */
    public function getContactById(int $id): ?Contact
    {
        return $this->repository->find($id);
    }

    /**
     * Create a new contact.
     */
    public function createContact(array $data): Contact
    {
        return $this->repository->create($data);
    }

    /**
     * Update an existing contact.
     */
    public function updateContact(int $id, array $data): Contact
    {
        return $this->repository->update($id, $data);
    }

    /**
     * Delete a contact.
     */
    public function deleteContact(int $id): bool
    {
        return $this->repository->delete($id);
    }

    /**
     * Get contacts by company.
     */
    public function getContactsByCompany(int $companyId): Collection
    {
        return $this->repository->getContactsByCompany($companyId);
    }

    /**
     * Search contacts by name or email.
     */
    public function searchContacts(string $search): Collection
    {
        return $this->repository->searchContacts($search);
    }

    /**
     * Get contact statistics.
     */
    public function getContactStatistics(): array
    {
        return $this->repository->getContactStatistics();
    }
}

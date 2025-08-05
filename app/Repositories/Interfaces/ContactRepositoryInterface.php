<?php

namespace App\Repositories\Interfaces;

use App\Models\Contact;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface ContactRepositoryInterface
{
    /**
     * Get all contacts.
     */
    public function all(): Collection;

    /**
     * Find contact by ID.
     */
    public function find(int $id): ?Contact;

    /**
     * Find contact by ID or throw exception.
     */
    public function findOrFail(int $id): Contact;

    /**
     * Create a new contact.
     */
    public function create(array $data): Contact;

    /**
     * Update an existing contact.
     */
    public function update(int $id, array $data): Contact;

    /**
     * Delete a contact.
     */
    public function delete(int $id): bool;

    /**
     * Get paginated contacts with filters.
     */
    public function paginate(array $filters = []): LengthAwarePaginator;

    /**
     * Get contacts by company.
     */
    public function getContactsByCompany(int $companyId): Collection;

    /**
     * Search contacts by name or email.
     */
    public function searchContacts(string $search): Collection;

    /**
     * Get contact statistics.
     */
    public function getContactStatistics(): array;
}

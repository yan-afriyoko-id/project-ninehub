<?php

namespace App\Services\Interfaces;

use App\Models\Contact;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface ContactServiceInterface
{
    /**
     * Get all contacts with optional filters.
     */
    public function getAllContacts(array $filters = []): LengthAwarePaginator;

    /**
     * Get contact by ID.
     */
    public function getContactById(int $id): ?Contact;

    /**
     * Create a new contact.
     */
    public function createContact(array $data): Contact;

    /**
     * Update an existing contact.
     */
    public function updateContact(int $id, array $data): Contact;

    /**
     * Delete a contact.
     */
    public function deleteContact(int $id): bool;

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

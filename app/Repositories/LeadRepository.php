<?php

namespace App\Repositories;

use App\Models\Lead;
use App\Interfaces\LeadRepositoryInterface;

class LeadRepository implements LeadRepositoryInterface
{
    public function all(array $relations = []): iterable
    {
        return Lead::with($relations)->get();
    }

    public function create(array $data): Lead
    {
        return Lead::create($data);
    }

    public function getById($id, array $relations = []): ?Lead
    {
        return Lead::with($relations)->find($id);
    }

    public function update(Lead $Lead, array $data): Lead
    {
        $Lead->update($data);
        return $Lead;
    }
    public function delete($id): bool
    {
        $Lead = Lead::find($id);
        if ($Lead) {
            return $Lead->delete();
        }
        return false;
    }

    public function findByContactId(int $contactId): ?Lead
    {
        return Lead::where('contact', $contactId);
    }

}

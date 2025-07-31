<?php

namespace App\Services;

use App\Exceptions\Lead\LeadNotFoundException;
use App\Interfaces\LeadRepositoryInterface;
use App\Models\Lead;
use App\Services\contactService;
use App\Events\LeadCreated;

class LeadService
{
    protected $repo;
    protected $contactService;

    public function __construct(LeadRepositoryInterface $repo, contactService $contactService)
    {
        $this->repo = $repo;
        $this->contactService = $contactService;
    }


    public function getAllLeads()
    {
        return $this->repo->all(['contact']);
    }


    public function create(array $data): Lead
    {
        $Lead = $this->repo->create($data);
        return $Lead;
    }


    public function getLeadById($id): ?Lead
    {
        $Lead = $this->repo->getById($id, ['contact']);
        return $Lead;
    }

    public function update(Lead $Lead, array $data): Lead
    {
        return $this->repo->update($Lead, $data);
    }

    public function delete($id): bool
    {
        $deleted = $this->repo->delete($id);
        return true;
    }
}

<?php

namespace App\Services;

use App\Exceptions\Contact\ContactNotFoundException;
use App\Interfaces\ContactRepositoryInterface;
use App\Models\Contact;
use App\Services\UserService;
use App\Events\ContactCreated;

class ContactService
{
    protected $repo;
    protected $UserService;

    public function __construct(ContactRepositoryInterface $repo, UserService $UserService)
    {
        $this->repo = $repo;
        $this->UserService = $UserService;
    }


    public function getAllContacts()
    {
        return $this->repo->all();
    }


    public function create(array $data): Contact
    {
        $Contact = $this->repo->create($data);
        return $Contact;
    }



    public function getContactById($id): ?Contact
    {
        $Contact = $this->repo->getById($id);
        return $Contact;
    }

    public function update(Contact $Contact, array $data): Contact
    {
        return $this->repo->update($Contact, $data);
    }

    public function delete($id): bool
    {
        $deleted = $this->repo->delete($id);
        return true;
    }
}

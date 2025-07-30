<?php

namespace App\Repositories;

use App\Models\Contact;
use App\Interfaces\ContactRepositoryInterface;

class ContactRepository implements ContactRepositoryInterface
{
    public function all(): iterable
    {
        return Contact::all();
    }

    public function create(array $data): Contact
    {
        return Contact::create($data);
    }

    public function getById($id): ?Contact
    {
        return Contact::find($id);
    }

    public function update(Contact $Contact, array $data): Contact
    {
        $Contact->update($data);
        return $Contact;
    }
    public function delete($id): bool
    {
        $Contact = Contact::find($id);
        if ($Contact) {
            return $Contact->delete();
        }
        return false;
    }

    public function findByUserId(int $userId): ?Contact
    {
        return Contact::where('user_id', $userId)->first();
    }

}

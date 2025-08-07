<?php

namespace App\Interfaces;
use App\Models\Contact;

interface ContactRepositoryInterface
{
    public function all(): iterable;
    public function create(array $data): Contact;
    public function getById($id): ?Contact;
    public function update(Contact $Contact, array $data): Contact;
    public function delete($id): bool;

}

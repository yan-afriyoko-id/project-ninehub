<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreContactRequest;
use App\Http\Resources\ContactResource;
use App\Services\ContactService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    protected $service;

    public function __construct(ContactService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $Contacts = $this->service->getAllContacts();
        return ContactResource::collection($Contacts);
    }

    public function store(StoreContactRequest $request)
    {
        $Contact = $this->service->create($request->validated());
        return new ContactResource($Contact);
    }

    public function show($id)
    {
        $Contact = $this->service->getContactById($id);
        return new ContactResource($Contact);
    }

    public function update(StoreContactRequest $request, $id)
    {
        $Contact = $this->service->getContactById($id);
        $updated = $this->service->update($Contact, $request->validated());
        return new ContactResource($updated);
    }

    public function destroy($id)
    {
        $this->service->delete($id);
        return response()->noContent();
    }
}

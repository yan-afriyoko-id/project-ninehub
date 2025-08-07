<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Contact\StoreContactRequest;
use App\Http\Requests\Contact\UpdateContactRequest;
use App\Http\Resources\ContactResource;
use App\Services\Interfaces\ContactServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    private ContactServiceInterface $contactService;

    public function __construct(ContactServiceInterface $contactService)
    {
        $this->contactService = $contactService;
    }

    /**
     * Display a listing of contacts.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['search', 'company_id', 'per_page']);
            $contacts = $this->contactService->getAllContacts($filters);

            return response()->json([
                'success' => true,
                'data' => ContactResource::collection($contacts),
                'pagination' => [
                    'current_page' => $contacts->currentPage(),
                    'last_page' => $contacts->lastPage(),
                    'per_page' => $contacts->perPage(),
                    'total' => $contacts->total(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve contacts',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created contact.
     */
    public function store(StoreContactRequest $request): JsonResponse
    {
        try {
            $contact = $this->contactService->createContact($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Contact created successfully',
                'data' => new ContactResource($contact),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create contact',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified contact.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $contact = $this->contactService->getContactById($id);

            if (!$contact) {
                return response()->json([
                    'success' => false,
                    'message' => 'Contact not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => new ContactResource($contact),
                'message' => 'Contact retrieved successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve contact',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified contact.
     */
    public function update(UpdateContactRequest $request, int $id): JsonResponse
    {
        try {
            $contact = $this->contactService->updateContact($id, $request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Contact updated successfully',
                'data' => new ContactResource($contact),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update contact',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified contact.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $result = $this->contactService->deleteContact($id);

            if (!$result) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete contact',
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Contact deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete contact',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get contacts by company.
     */
    public function byCompany(int $companyId): JsonResponse
    {
        try {
            $contacts = $this->contactService->getContactsByCompany($companyId);

            return response()->json([
                'success' => true,
                'data' => ContactResource::collection($contacts),
                'message' => 'Contacts retrieved successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve contacts',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Search contacts.
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $search = $request->get('q', '');
            $contacts = $this->contactService->searchContacts($search);

            return response()->json([
                'success' => true,
                'data' => ContactResource::collection($contacts),
                'message' => 'Contacts retrieved successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to search contacts',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get contact statistics.
     */
    public function statistics(): JsonResponse
    {
        try {
            $statistics = $this->contactService->getContactStatistics();

            return response()->json([
                'success' => true,
                'data' => $statistics,
                'message' => 'Contact statistics retrieved successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve contact statistics',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

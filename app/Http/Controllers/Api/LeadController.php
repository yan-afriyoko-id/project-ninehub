<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLeadRequest;
use App\Http\Resources\LeadResource;
use App\Services\LeadService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    protected $service;

    public function __construct(LeadService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $Leads = $this->service->getAllLeads();
        return LeadResource::collection($Leads);
    }

    public function getLead(Request $request)
    {
        $user = $request->user();
        return new LeadResource($user->load('Lead')->Lead);
    }

    public function store(StoreLeadRequest $request)
    {
        $Lead = $this->service->create($request->validated());
        return new LeadResource($Lead);
    }

    public function show($id)
    {
        $Lead = $this->service->getLeadById($id);
        return new LeadResource($Lead);
    }

    public function update(StoreLeadRequest $request, $id)
    {
        $Lead = $this->service->getLeadById($id);
        $updated = $this->service->update($Lead, $request->validated());
        return new LeadResource($updated);
    }

    public function destroy($id)
    {
        $this->service->delete($id);
        return response()->noContent();
    }
}

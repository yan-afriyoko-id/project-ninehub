<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCompanyRequest;
use App\Http\Resources\CompanyResource;
use App\Services\CompanyService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    protected $service;

    public function __construct(CompanyService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $Companys = $this->service->getAllCompanys();
        return CompanyResource::collection($Companys);
    }

    public function getCompany(Request $request)
    {
        $user = $request->user();
        return new CompanyResource($user->load('Company')->Company);
    }

    public function store(StoreCompanyRequest $request)
    {
        $userId = Auth::id();
        $Company = $this->service->create($request->validated(), $userId);
        return new CompanyResource($Company);
    }

    public function show($id)
    {
        $Company = $this->service->getCompanyById($id);
        return new CompanyResource($Company);
    }

    public function update(StoreCompanyRequest $request, $id)
    {
        $Company = $this->service->getCompanyById($id);
        $updated = $this->service->update($Company, $request->validated());
        return new CompanyResource($updated);
    }

    public function destroy($id)
    {
        $this->service->delete($id);
        return response()->noContent();
    }
}

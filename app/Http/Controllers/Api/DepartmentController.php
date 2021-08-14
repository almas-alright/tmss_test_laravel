<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Resources\DepartmentResource;
use App\Models\Department;
use App\TestAssignment\Interfaces\DepartmentRepositoryInterface;
use App\TestAssignment\Services\ApiResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DepartmentController extends ApiController
{
    protected $department;

    public function __construct(DepartmentRepositoryInterface $department)
    {
        parent::__construct();
        $this->department = $department;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(ApiResponseService $apiResponseService)
    {
        $data = $this->department->getAll();
        $department = DepartmentResource::collection($data);
        return $apiResponseService->efflux($department);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request, ApiResponseService $apiResponseService)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:departments,name',
        ],[
            'name.uniquie' => 'name exists'
        ]);

        if($validator->fails()){
            return $apiResponseService->efflux(null,$validator->messages(), 422);
        }
        $data = $this->department->create($validator->validated());

        if ($data->wasRecentlyCreated){
            $department = new DepartmentResource($data);
            return $apiResponseService->efflux($department);
        } else {
            return $apiResponseService->efflux(null);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Department  $department
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Department $department, ApiResponseService $apiResponseService)
    {
        $data = new DepartmentResource($department);
        return $apiResponseService->efflux($data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Department  $department
     * @return \Illuminate\Http\Response
     */
    public function edit(Department $department)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Department  $department
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Department $department, ApiResponseService $apiResponseService)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:departments,name',
        ],[
            'name.uniquie' => 'name exists'
        ]);

        if($validator->fails()){
            return $apiResponseService->efflux(null,$validator->messages(),422);
        }
        $data = $this->department->update($department->id, $validator->validated());

        if ($data->wasChanged()){
            $department = new DepartmentResource($data);
            return $apiResponseService->efflux($department);
        } else {
            return $apiResponseService->efflux(null);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Department  $department
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Department $department, ApiResponseService $apiResponseService)
    {
        $deleted = $this->department->delete($department->id);
        if($deleted){
            return $apiResponseService->efflux(['message' => 'Trashed']);
        } else {
            return $apiResponseService->efflux(null);
        }
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Resources\BatchResource;
use App\Models\Batch;
use App\TestAssignment\Interfaces\BatchRepositoryInterface;
use App\TestAssignment\Services\ApiResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BatchController extends ApiController
{
    protected $batch;
    public function __construct(BatchRepositoryInterface $batch)
    {
        parent::__construct();
        $this->batch = $batch;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(ApiResponseService $apiResponseService)
    {
        $data = $this->batch->getAll();
        $batch = BatchResource::collection($data);
        return $apiResponseService->efflux($batch);
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
            'name' => 'required|unique:batches,name',
        ],[
            'name.uniquie' => 'name exists'
        ]);

        if($validator->fails()){
            return $apiResponseService->efflux(null, $validator->messages(), 422);
        }
        $data = $this->batch->create($validator->validated());


        if ($data->wasRecentlyCreated){
            $batch = new BatchResource($data);
            return $apiResponseService->efflux($batch);
        } else {
            return $apiResponseService->efflux(null);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Batch  $batch
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Batch $batch, ApiResponseService $apiResponseService)
    {
        $data = new BatchResource($batch);
        return $apiResponseService->efflux($data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Batch  $batch
     * @return \Illuminate\Http\Response
     */
    public function edit(Batch $batch)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Batch  $batch
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Batch $batch, ApiResponseService $apiResponseService)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:batches,name',
        ],[
            'name.uniquie' => 'name exists'
        ]);

        if($validator->fails()){
            return $apiResponseService->efflux(null, $validator->messages(), 422);
        }
        $data = $this->batch->update($batch->id, $validator->validated());


        if ($data->wasChanged()){
            $batch = new BatchResource($data);
            return $apiResponseService->efflux($batch);
        } else {
            return $apiResponseService->efflux(null);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Batch  $batch
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Batch $batch, ApiResponseService $apiResponseService)
    {
        $deleted = $this->batch->delete($batch->id);
        if($deleted){
            return $apiResponseService->efflux(['message' => 'Trashed']);
        } else {
            return $apiResponseService->efflux('');
        }
    }
}

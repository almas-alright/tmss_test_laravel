<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Resources\ResultResource;
use App\Models\Result;
use App\TestAssignment\Interfaces\ResultRepositoryInterface;
use App\TestAssignment\Services\ApiResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ResultController extends ApiController
{
    protected $result;

    public function __construct(ResultRepositoryInterface $result)
    {
        parent::__construct();
        $this->result = $result;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request, ApiResponseService $apiResponseService): \Illuminate\Http\JsonResponse
    {
        $where = [];
        if($request->has('student') && $request->get('student') != ""){
            $where['results.student_id'] = $request->get('student');
        }
        if($request->has('department') && $request->get('department') != ""){
            $where['students.department_id'] = $request->get('department');
        }
        $results = $this->result->datatable($where);
        if($request->has('gpa') && $request->get('gpa') != ""){
            $results->where('results.gpa', '=', $request->get('gpa'));
        }
        $data = $results->get();
        return $apiResponseService->efflux($data);
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, ApiResponseService $apiResponseService)
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|unique:results,student_id', 'gpa' => 'required', 'published' => 'required|date'
        ],[]);

        if($validator->fails()){
            return $apiResponseService->efflux(null,$validator->messages(), 422);
        }
        $data = $this->result->create($validator->validated());

        if ($data->wasRecentlyCreated){
            $studentUpdate = new ResultResource($data);
            return $apiResponseService->efflux($studentUpdate);
        } else {
            return $apiResponseService->efflux(null);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Result  $result
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Result $result, ApiResponseService $apiResponseService)
    {
        $data = new ResultResource($result);
        return $apiResponseService->efflux($data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Result  $result
     * @return \Illuminate\Http\Response
     */
    public function edit(Result $result)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Result  $result
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Result $result, ApiResponseService $apiResponseService)
    {
        $validator = Validator::make($request->all(), [
            'gpa' => 'required',
            'published' => 'required',
//            'student_id' => 'unique:results,student_id,'.$request->id
        ],[]);

        if($validator->fails()){
            return $apiResponseService->efflux(null,$validator->messages(), 422);
        }
        $data = $this->result->update($result->id, $validator->validated());

        if ($data->wasChanged()){
            $studentUpdate = new ResultResource($data);
            return $apiResponseService->efflux($studentUpdate);
        } else {
            return $apiResponseService->efflux(null);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Result  $result
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Result $result, ApiResponseService $apiResponseService)
    {
        $deleted = $this->result->delete($result->id);
        if($deleted){
            return $apiResponseService->efflux(['message' => 'Trashed']);
        } else {
            return $apiResponseService->efflux(null);
        }
    }
}

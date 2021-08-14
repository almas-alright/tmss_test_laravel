<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Resources\StudentResource;
use App\Models\Student;
use App\TestAssignment\Interfaces\StudenRepositoryInterface;
use App\TestAssignment\Services\ApiResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StudentController extends ApiController
{
    protected $student;

    public function __construct(StudenRepositoryInterface $student)
    {
        parent::__construct();
        $this->student = $student;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request, ApiResponseService $apiResponseService)
    {
        $where = [];
        if($request->has('batch') && $request->get('batch') != ""){
            $where['students.batch_id'] = $request->get('batch');
        }
        if($request->has('department') && $request->get('department') != ""){
            $where['students.department_id'] = $request->get('department');
        }
        $student = $this->student->datatable($where);
        if($request->has('name') && $request->get('name') != ""){
            $name = $request->get('name');
            $student->where('students.name', 'like', '%'.$name.'%');
        }
        if($request->has('gpa') && $request->get('gpa') != ""){
            $student->where('results.gpa', '=', $request->get('gpa'));
        } else {
            if (($request->has('gpa_min') && $request->get('gpa_min') != "") && ($request->has('gpa_max') && $request->get('gpa_max') != "")) {
                $student->whereBetween('results.gpa', [$request->get('gpa_min'), $request->get('gpa_max')]);
            } elseif ($request->has('gpa_min') && $request->get('gpa_min') != "") {
                $student->where('results.gpa', '>', $request->get('gpa_min'));
            } elseif ($request->has('gpa_max') && $request->get('gpa_max') != "") {
                $student->where('results.gpa', '<=', $request->get('gpa_max'));
            }
        }
        $data = $student->get();
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
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request, ApiResponseService $apiResponseService)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'department_id' => 'required',
            'batch_id' => 'required'
        ],[]);

        if($validator->fails()){
            return $apiResponseService->efflux(null,$validator->messages(), 422);
        }
        $data = $this->student->create($validator->validated());

        if ($data->wasRecentlyCreated){
            $student = new StudentResource($data);
            return $apiResponseService->efflux($student);
        } else {
            return $apiResponseService->efflux(null);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Student $student, ApiResponseService $apiResponseService)
    {
        $data = new StudentResource($student);
        return $apiResponseService->efflux($data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function edit(Student $student)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Student $student, ApiResponseService $apiResponseService)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required', 'department_id' => 'required', 'batch_id' => 'required'
        ],[]);

        if($validator->fails()){
            return $apiResponseService->efflux(null,$validator->messages(), 422);
        }
        $data = $this->student->update($student->id, $validator->validated());

        if ($data->wasChanged()){
            $studentUpdate = new StudentResource($data);
            return $apiResponseService->efflux($studentUpdate);
        } else {
            return $apiResponseService->efflux(null);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Student $student, ApiResponseService $apiResponseService): \Illuminate\Http\JsonResponse
    {
        $deleted = $this->student->delete($student->id);
        if($deleted){
            return $apiResponseService->efflux(['message' => 'Trashed']);
        } else {
            return $apiResponseService->efflux(null);
        }
    }
}

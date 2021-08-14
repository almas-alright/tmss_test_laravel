<?php

namespace App\TestAssignment\Eloquent;

use App\Models\Result;
use Illuminate\Support\Facades\DB;

class ResultRepository extends AbstractRepository implements \App\TestAssignment\Interfaces\ResultRepositoryInterface
{

    public function __construct(Result $model)
    {
        parent::__construct($model);
    }

    /**
     * @inheritDoc
     */
    public function getAll()
    {
        return $this->model->all();
    }

    /**
     * @inheritDoc
     */
    public function find($id)
    {
        return $this->model->find($id);
    }

    /**
     * @inheritDoc
     */
    public function create(array $attributes)
    {
        return $this->model->create($attributes);
    }

    /**
     * @inheritDoc
     */
    public function update($id, array $attributes)
    {
        $result = $this->model->find($id);
        if($result) {
            $result->update($attributes);
            return $result;
        }
        return false;
    }

    /**
     * @inheritDoc
     */
    public function delete($id)
    {
        $result = $this->model->find($id);
        if($result) {
            $result->delete();
            return true;
        }
        return false;
    }

    public function datatable(array $where)
    {
        $student = DB::table('results')
            ->join('students', 'results.student_id', 'students.id')
            ->leftjoin('batches', 'students.batch_id', 'batches.id')
            ->leftJoin('departments', 'students.department_id', 'departments.id')
            ->select('results.*',

                'students.id as student_id',
                'students.name as student_name',
                'students.department_id as student_department',
                'students.batch_id as student_batch',

                'departments.name as department_name',
                'batches.name as batch_name'
            )->whereNull('results.deleted_at');
        $student->where($where);
        return $student;
    }
}

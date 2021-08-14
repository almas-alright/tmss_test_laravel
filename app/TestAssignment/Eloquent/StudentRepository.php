<?php

namespace App\TestAssignment\Eloquent;
use App\Models\Student;
use App\TestAssignment\Interfaces\StudenRepositoryInterface;
use Illuminate\Support\Facades\DB;

class StudentRepository extends AbstractRepository implements StudenRepositoryInterface
{

    public function __construct(Student $model)
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
        $student = DB::table('students')
            ->join('departments', 'students.department_id', 'departments.id')
            ->join('batches', 'students.batch_id', 'batches.id')
            ->leftJoin('results', 'students.id', 'results.student_id')
            ->select('students.*', 'results.gpa', 'departments.name as department_name', 'batches.name as batch_name')->whereNull('students.deleted_at');
        $student->where($where);
        return $student;
    }
}

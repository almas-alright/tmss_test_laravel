<?php

namespace App\TestAssignment\Eloquent;

use App\Models\Batch;
use App\TestAssignment\Interfaces\BatchRepositoryInterface;

class BatchRepository extends AbstractRepository implements  BatchRepositoryInterface
{

    public function __construct(Batch $model)
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
}

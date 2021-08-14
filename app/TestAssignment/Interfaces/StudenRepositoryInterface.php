<?php

namespace App\TestAssignment\Interfaces;

interface StudenRepositoryInterface extends BaseRepositoryInterface
{
    public function datatable(array $where);
}

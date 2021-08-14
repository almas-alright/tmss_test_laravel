<?php

namespace App\TestAssignment\Interfaces;

interface ResultRepositoryInterface extends BaseRepositoryInterface
{
    public function datatable(array $where);
}

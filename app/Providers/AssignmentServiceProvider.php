<?php

namespace App\Providers;

use App\TestAssignment\Eloquent\BatchRepository;
use App\TestAssignment\Eloquent\DepartmentRepository;
use App\TestAssignment\Eloquent\ResultRepository;
use App\TestAssignment\Eloquent\StudentRepository;
use App\TestAssignment\Interfaces\BatchRepositoryInterface;
use App\TestAssignment\Interfaces\DepartmentRepositoryInterface;
use App\TestAssignment\Interfaces\ResultRepositoryInterface;
use App\TestAssignment\Interfaces\StudenRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class AssignmentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(DepartmentRepositoryInterface::class, DepartmentRepository::class);
        $this->app->bind(BatchRepositoryInterface::class, BatchRepository::class);
        $this->app->bind(StudenRepositoryInterface::class, StudentRepository::class);
        $this->app->bind(ResultRepositoryInterface::class, ResultRepository::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}

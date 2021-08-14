<?php


namespace App\TestAssignment\Services;


class ApiResponseService
{
    public function efflux($data, $error = null, $status = 200){
         if ($data){
            return response()->json([
                'success' => true,
                'data' => $data,
                'errors' => $error
            ], $status);
        } else {
            return response()->json([
                'success' => false,
                'data' => null,
                'errors' => $error
            ], $status);
        }
    }

}

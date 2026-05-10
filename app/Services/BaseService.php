<?php 

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Exception;

abstract class BaseService
{
    protected function execute(callable $callback)
    {
        DB::beginTransaction();
        try {
            $result = $callback();
            DB::commit();
            return $result;
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            throw $e;
        }
    }
}
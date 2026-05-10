<?php 

namespace App\Repositories\Contracts;

interface ProductRepositoryInterface
{
    public function getBranchProducts($branchId);
    public function findBySku($sku);
}
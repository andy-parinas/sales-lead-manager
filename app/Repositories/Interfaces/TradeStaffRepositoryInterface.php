<?php


namespace App\Repositories\Interfaces;


interface TradeStaffRepositoryInterface
{

    public function getAll(array $params);

    public function searchAll($search);

}

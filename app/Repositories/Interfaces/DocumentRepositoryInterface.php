<?php


namespace App\Repositories\Interfaces;


interface DocumentRepositoryInterface
{
    public function getAllByLeadId(Array $params, $leadId);
}

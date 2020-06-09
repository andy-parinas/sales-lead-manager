<?php


namespace App\Repositories;


use App\SalesStaff;

class SalesStaffRepository implements Interfaces\SalesStafRepositoryInterface
{

    public function getAll(array $params)
    {
        if(key_exists('search', $params) && key_exists('on', $params))
        {
            return SalesStaff::with('franchise')->where($params['on'], 'LIKE', '%' . $params['search'] . '%')
                ->orderBy($params['column'], $params['direction'])
                ->paginate($params['size']);
        }

        return SalesStaff::with('franchise')->orderBy($params['column'], $params['direction'])
            ->paginate($params['size']);
    }
}

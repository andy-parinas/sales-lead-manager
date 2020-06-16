<?php


namespace App\Repositories;


use Illuminate\Support\Facades\DB;

class TradeStaffRepository implements Interfaces\TradeStaffRepositoryInterface
{

    public function getAll(array $params)
    {
        $query = DB::table('trade_staff')
            ->join('franchises', 'franchises.id', '=', 'trade_staff.franchise_id')
            ->join('trade_types', 'trade_types.id', '=', 'trade_staff.trade_type_id')
            ->select('trade_staff.id',
                'trade_staff.franchise_Id',
                'trade_staff.trade_type_id',
                'trade_staff.first_name',
                'trade_staff.last_name',
                'trade_staff.email',
                'trade_staff.contact_number',
                'trade_staff.company',
                'trade_staff.abn',
                'trade_staff.builders_license',
                'trade_staff.status',
                'franchises.franchise_number',
                'trade_types.name as trade_type',
                'trade_staff.created_at as created_at'
            );



        if(key_exists('search', $params) && key_exists('on', $params))
        {
            return  $query->where($params['on'], 'LIKE', '%' . $params['search'] . '%')
                ->orderBy($params['column'], $params['direction'])
                ->paginate($params['size']);
        }

        return $query->orderBy($params['column'], $params['direction'])
            ->paginate($params['size']);
    }
}

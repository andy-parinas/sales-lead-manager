<?php


namespace App\Repositories;


use Illuminate\Support\Facades\DB;

class ReportRepository implements Interfaces\ReportRepositoryInterface
{

    public function generateSalesSummary($start_date, $end_date)
    {
        return DB::select("SELECT concat(sales_staff.first_name, ' ', sales_staff.last_name) as salesStaff,
            franchises.franchise_number as franchiseNumber,
            count(case appointments.outcome when 'success' then 1 else null end) as SuccessCount,
            count( IF (contracts.contract_price > 0 and appointments.outcome = 'success' , 1, null) ) as numberOfSales,
            count(leads.id) as numberOfLeads,
            (count( IF (contracts.contract_price > 0 and appointments.outcome = 'success' , 1, null) ) / count(leads.id)) * 100 as conversionRate,
            avg(contracts.contract_price) as averageSalesPrice,
            sum(contracts.total_contract) as totalContracts
            FROM leads LEFT JOIN appointments ON leads.id = appointments.lead_id
            LEFT JOIN job_types ON leads.id = job_types.lead_id
            LEFT JOIN contracts ON leads.id = contracts.lead_id
            LEFT JOIN (sales_staff LEFT JOIN franchises ON franchises.id = sales_staff.franchise_id) ON job_types.sales_staff_id = sales_staff.id
            WHERE leads.lead_date >= :start_date AND leads.lead_date <= :end_date
            GROUP BY sales_staff.last_name, sales_staff.first_name, franchises.franchise_number",
            [
                'start_date' => $start_date,
                'end_date' => $end_date
            ]);
    }
}

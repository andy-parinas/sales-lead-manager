<?php


namespace App\Traits;


trait ReportComputer
{

    protected function computeTotal($results)
    {
        $totalNumberOfSales = 0;
        $totalNumberOfLeads = 0;
        $totalConversionRate = 0;
        $grandTotalContracts = 0;
        $grandTotalAveragePrice = 0;


        foreach ($results as $result){
            $totalNumberOfSales = $totalNumberOfSales + $result->numberOfSales;
            $totalNumberOfLeads = $totalNumberOfLeads + $result->numberOfLeads;
            $totalConversionRate = $totalConversionRate + $result->conversionRate;
            $grandTotalContracts = $grandTotalContracts + $result->totalContracts;
            $grandTotalAveragePrice = $grandTotalAveragePrice + $result->averageSalesPrice;
        }

        $resultLength = count($results);

        return [
            'totalNumberOfSales' => $totalNumberOfSales,
            'totalNumberOfLeads' => $totalNumberOfLeads,
            'averageConversionRate' => $totalConversionRate / $resultLength,
            'grandTotalContracts' => $grandTotalContracts,
            'grandAveragePrice' => $grandTotalAveragePrice / $resultLength,
            'averageNumberOfLeads' => $totalNumberOfLeads / $resultLength,
            'averageNumberOfSales' => $totalNumberOfSales / $resultLength,
            'averageTotalContract' => $grandTotalContracts / $resultLength,
        ];
    }

}

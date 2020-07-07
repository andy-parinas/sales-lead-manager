SELECT design_assessors.last_name,  
count(case appointments.outcome when 'success' then 1 else null end) as SuccessCount,
count( IF (contracts.contract_price > 0 and appointments.outcome = 'success' , 1, null) ) as SuccessCountWithContract,
count(leads.id) as LeadsCount,
(count( IF (contracts.contract_price > 0 and appointments.outcome = 'success' , 1, null) ) / count(leads.id)) * 100 as CovertionRate,
count(contracts.id) as NumberOfContracts,
avg(contracts.contract_price) as AverageSalesPrice,
sum(contracts.total_contract) as TotalContracts
FROM leads LEFT JOIN appointments ON leads.id = appointments.lead_id
LEFT JOIN job_types ON leads.id = job_types.lead_id
LEFT JOIN contracts ON leads.id = contracts.lead_id
LEFT JOIN design_assessors ON job_types.design_assessor_id = design_assessors.id
GROUP BY design_assessors.last_name
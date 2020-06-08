<?php


namespace App\Repositories;


use App\Document;

class DocumentRepository implements Interfaces\DocumentRepositoryInterface
{

    public function getAllByLeadId(array $params, $leadId)
    {

        if(key_exists('search', $params))
        {
            if (key_exists('size', $params) && $params['size'] > 0){

                return Document::where(['lead_id', '=', $leadId], ['title','LIKE', '%' . $params['search'] . '%'])
                    ->orderBy($params['column'], $params['direction'])
                    ->paginate($params['size']);

            }else {

                return Document::where(['lead_id', '=', $leadId], ['title','LIKE', '%' . $params['search'] . '%'])
                    ->orderBy($params['column'], $params['direction'])
                    ->get();

            }

        }else {

            if (key_exists('size', $params) && $params['size'] > 0){
                return Document::where('lead_id', '=', $leadId)
                ->orderBy($params['column'], $params['direction'])->paginate($params['size']);
            }else {
                return Document::where('lead_id', '=', $leadId)
                    ->orderBy($params['column'], $params['direction'])->get();
            }

        }
    }
}

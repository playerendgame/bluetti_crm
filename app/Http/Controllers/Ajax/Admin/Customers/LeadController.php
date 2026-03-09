<?php

namespace App\Http\Controllers\Ajax\Admin\Customers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    
    public function list(Request $request){
        $input = $request->all();
        $column = $request->column;
        if(strcmp($column, "created_at_s") == 0) {
            $column = 'created_at';
        }

        $query = Leads::orderBy($column, $request->order);

        if(isset($input['keyword'])){
            $query->where('name', 'like', '%' . $input['keyword'] . "%");
            $query->orWhere('email', 'like', '%' . $input['keyword'] . "%");
            $query->orWhere('number', 'like', '%' . $input['keyword'] . "%");

        }

        $leads = $query->paginate($request->per_page);

        foreach ($leads as $lead) {
            $lead->created_at_s = Carbon::parse($lead->created_at)->toDayDateTimeString();
        }

        return array("success" => true, "message" => "", "data" => $leads);
    }

}

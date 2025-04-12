<?php

namespace App\Http\Controllers\Ajax\Admin\Referral;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Referral;
use Carbon\Carbon;

class ReferralController extends Controller
{
    public function getReferral(Request $request)
    {
        $query = Referral::orderBy('name', 'asc');

        $referrals = $query->get();

        return array("success" => true, "message" => "", "data" => $referrals);
    }

    public function create(Request $request)
    {
        $validation_rules = [
            'name' => 'required',
            'email' => 'required|unique:referrals',
        ];

        $request->validate($validation_rules);

        $input = $request->all();

        $referral = new Referral();
        $referral->name = $input["name"];
        $referral->email = $input["email"];
        $referral->save();

        return array("success" => true, "message" => "Referral Created Succesfully!", "data" => null);
    }

    public function list(Request $request)
    {
        $input = $request->all();
        
        $column = $request->column;

        if ($column === "created_at_s") {
            $column = "created_at";
        }

        $query = Referral::orderBy($column, $request->order);

        if (isset($input["keyword"])) {
            $query->where(function ($query) use ($input) {
                $query->where("name", "like", "%" . $input["keyword"] . "%")
                      ->orWhere("email", "like", "%" . $input["keyword"] . "%");
            });
        }

        $referrals = $query->paginate($request->per_page);

        foreach ($referrals as $referral) {
            $referral->created_at_s = Carbon::parse($referral->created_at)->format("M j, Y g:i A");
        }

        return array("success" => true, "message" => "", "data" => $referrals);
    }
}

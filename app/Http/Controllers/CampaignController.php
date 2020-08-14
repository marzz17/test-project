<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Campaigns;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
class CampaignController extends Controller
{
       public function getcampaigns(){
       $campaigns = DB::table('campaigns')->select(DB::raw('id, name'))->get();
        return response()->json(['campaigns' => $campaigns ]);
        
    }
       public function savecampaigns(Request $request){ 
        $campaigns = new Campaigns;
        $campaigns->name = $request->name;
        $campaigns->save();
        return response("Created", 201);
    }
        public function destroycampaigns($id){ 
        $campaigns = Campaigns::find($id);
        $campaigns->delete();
        return response("deleted", 200);
    }
   
}
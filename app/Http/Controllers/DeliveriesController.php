<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Deliveries;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class DeliveriesController extends Controller
{
    public function savedeliveries(Request $request) {
        DB::transaction(function () use ($request){
               foreach ($request->data as $item) {
                    $delivery = new Deliveries;
                    $delivery->recipient = $item['recipient'];
                    $delivery->subject = $item['subject'];
                    $delivery->message = $item['message'];
                    $delivery->contacts_id = $item['contact'];
                    $delivery->template_id = $item['template'];
                    $delivery->status = $item['status'];
                    $delivery->save();
                }
                return response("Created", 201);
        });
    }
   
           
}
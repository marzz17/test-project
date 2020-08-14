<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Templates;
use App\Contacts;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
class TemplatesController extends Controller
{
      public function gettemplates(){
       $templates = DB::table('templates')
            ->join('campaigns', 'templates.campaign_id', '=', 'campaigns.id')
            ->select('templates.id, templates.name','templates.subject','templates.message','templates.campaign_id','campaigns.name as campaign_name')
            ->get();
        return response()->json(['templates' => $templates ]);
        
    }
       public function savetemplates(Request $request){ 
        
             DB::transaction(function () use ($request){
                try{
                    $contact = serialize($request->data);    
                    $contacts = new Contacts;
                    $contacts->name = $request->template['templatename'];
                    $contacts->description =  $contact;
                    $contacts->save();
                    $contactidused = $contacts->id;

                    $templates = new Templates;
                    $templates->name = $request->template['templatename'];
                    $templates->subject = $request->template['subject'];
                    $templates->message = $request->template['messages'];
                    $templates->campaign_id = $request->template['campaign'];
                    $templates->contact_id = $contactidused ;
                    $templates->save();
                    return response("Created", 201);
            } catch(Exception $e){
             return response("error", 500);
           }
            });
           
       
            
    }
     
}
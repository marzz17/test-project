<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Contacts;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
class GlobalController extends Controller
{
    public function createcontactstable(Request $request){
        Schema::dropIfExists('Contacts');
        Schema::create('Contacts', function (Blueprint $table) {
        $table->increments('id');
        $table->text('description');
        $table->timestamps();
    });
    return response("Created", 201);
    }
    public function savecontacts(Request $request){
        $contact = serialize($request->data);    
        $contacts = new Contacts;
        $contacts->name =  "sample";
        $contacts->description =  $contact;
        $contacts->save();
        return response("Created", 201);
    }
     public function getcontacts($id){
        $contacts = DB::table('contacts')->select(DB::raw('description'))->where('id','=', $id)->get();
        $data = unserialize($contacts[0]->description);  
        return response()->json(['contacts' => $data ]);
        
    }
}
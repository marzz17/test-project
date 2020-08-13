<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Contacts;
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
        $contacts->description =  $contact;
        $contacts->save();
        return response("Created", 201);
    }
     public function getcontacts($id){
        $contact = serialize($request->data);
    // dd($contact);       
        $contacts = new Contacts;
        $contacts->description =  $contact;
        $contacts->save();
        return response("Created", 201);
    }
}
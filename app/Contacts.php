<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contacts extends Model
{
      protected $table = 'Contacts';
      public $timestamps = true;
      protected $guarded = ['id','description','created_at','updated_at'];  
}
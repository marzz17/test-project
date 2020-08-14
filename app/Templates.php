<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Templates extends Model
{
     protected $table = 'templates';
      public $timestamps = true;
      protected $guarded = ['id','name','subject','message','campaign_id','created_at','updated_at'];  
}
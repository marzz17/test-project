<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Deliveries extends Model
{
    protected $table = 'Deliveries';
      public $timestamps = true;
      protected $guarded = ['id','recipient','subject','message','contacts_id','template_id','created_at','updated_at'];  
}
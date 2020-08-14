<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Campaigns extends Model
{
    protected $table = 'Campaigns';
    public $timestamps = true;
    protected $guarded = ['id','name','created_at','updated_at'];  
}
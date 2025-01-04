<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visitor extends Model
{
    
    protected $table = 'visitors';


    protected $fillable = [
        'ip', 'country', 'region', 'city', 'zip', 'lat', 'lon', 'isp','flag'
    ];

}

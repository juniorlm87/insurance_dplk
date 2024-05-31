<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cligateway extends Model
{
    use HasFactory;
    protected $fillable = [
        'route_name', 'parameter', 'access_status', 'access_date','create_by','last_change_by'
    ];
}

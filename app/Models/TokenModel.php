<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TokenModel extends Model
{
    protected  $table='token';
    protected  $primaryKey='id';
    public     $timestamps=false;
}

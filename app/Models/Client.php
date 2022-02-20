<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $guarded=[];

    protected $casts=[
        'phone'=>'array'
    ];

    public function getNameAttribute($value)
    {

        return ucfirst($value);

    }

    public function order()
    {
        return $this->hasMany(order::class);
    }
}

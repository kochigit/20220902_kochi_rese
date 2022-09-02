<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $hidden = [
        'id',
        'created_at',
        'updated_at',
    ];

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    public function favorites() 
    {
        return $this->hasMany('App\Models\Favorite', 'restaurant_uuid', 'uuid');
    }

    public function reservations()
    {
        return $this->hasMany('App\Models\Reservation', 'restaurant_uuid', 'uuid');
    }

    public function managements()
    {
        return $this->hasMany('App\Models\Management', 'restaurant_uuid', 'uuid');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function restaurant()
    {
        return $this->hasOne('App\Models\Restaurant', 'uuid', 'restaurant_uuid');
        // belongsToの方が正しい気がするが、belongsToではデータを引っ張ってこれなかった。
    }

    public function user()
    {
        return $this->hasOne('App\Models\User', 'uuid', 'user_uuid');
    }
}

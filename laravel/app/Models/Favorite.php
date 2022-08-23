<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function restaurant()
    {
        return $this->hasOne('App\Models\Restaurant', 'uuid', 'restaurant_uuid');
        // belongsToの方が正しい気がするが、belongsToではデータを引っ張ってこれなかった。
    }
}

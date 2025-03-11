<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Achievement extends Model
{
    protected $fillable = ['name', 'description', 'title'];

    public function images()
    {
        return $this->hasMany(AchievementImage::class);
    }
}

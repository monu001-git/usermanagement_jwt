<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class achievementImage extends Model
{
    use SoftDeletes;

    protected $fillable = ['achievement_id', 'image_path'];

    public function achievement()
    {
        return $this->belongsTo(Achievement::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class achievement extends Model
{
    use SoftDeletes;
    
    protected $fillable = ['title', 'description'];

    public function images()
    {
        return $this->hasMany(achievementImage::class);
    }

}

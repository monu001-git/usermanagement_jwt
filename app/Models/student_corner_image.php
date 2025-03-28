<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class student_corner_image extends Model
{
    use SoftDeletes;

    protected $fillable = ['student_corner_id', 'image_path','image_title'];

    public function studentCorner()
    {
        return $this->belongsTo(StudentCorner::class);
    }
}

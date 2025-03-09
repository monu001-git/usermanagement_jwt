<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class page_image extends Model
{
    use SoftDeletes;

    public function page()
    {
        return $this->belongsTo(Page::class);
    }
}

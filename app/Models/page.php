<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class page extends Model
{
    use SoftDeletes;

    public function pageContent()
    {
        return $this->hasOne(PageContent::class);
    }

    public function pageImage()
    {
        return $this->hasOne(PageImage::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $fillable = [
        'slug',
        'locale',
        'title',
        'description',
        'body_class',
        'header_html',
        'content_html',
        'footer_html',
    ];
}

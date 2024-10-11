<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;
    protected $fillable = [
        'source_id',
        'title',
        'author',
        'content',
        'url',
        'published_at',
        'category',
    ];

    public function source()
    {
        return $this->belongsTo(Source::class);
    }
}

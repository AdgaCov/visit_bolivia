<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArticleImage extends Model
{
    protected $table = 'article_images';

    protected $fillable = [
        'article_id',
        'image_url',
        'alt_text',
        'is_main',
    ];

    public $timestamps = false;

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class, 'article_id');
    }
}





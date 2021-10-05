<?php

namespace App\Models;

use GrahamCampbell\Markdown\Facades\Markdown;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'author_name', 'author_email', 'author_url', 'body'
    ];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function getDateAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    public function getBodyHtmlAttribute()
    {
        return Markdown::convertToHtml(e($this->body));
    }
}

<?php

namespace App\Models;

use Carbon\Carbon;
use GrahamCampbell\Markdown\Facades\Markdown;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Laratrust\Contracts\Ownable;

class Post extends Model implements Ownable
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'title', 'slug', 'excerpt', 'body', 'image', 'published_at', 'category_id'
    ];

    protected $dates = ['published_at'];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    public function author()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function setPublishedAtAttribute($value)
    {
        $this->attributes['published_at'] = $value ?: null;
    }

    public function setImageAttribute($value)
    {
        if (!app()->runningInConsole()) {
            if ($value instanceof UploadedFile) {
                $this->deleteImage();
                $success = $value->move(config('cms.image.directory'), $value->getClientOriginalName());
                if ($success) {
                    Image::make(config('cms.image.directory') . '/' . $value->getClientOriginalName())
                        ->resize(config('cms.image.thumbnail.width'), config('cms.image.thumbnail.height'))
                        ->save(config('cms.image.directory') . '/' . str_replace(".{$value->getClientOriginalExtension()}", "_thumb.{$value->getClientOriginalExtension()}", $value->getClientOriginalName()));
                }
                $this->attributes['image'] = $value->getClientOriginalName();
            }
        } else {
            $this->attributes['image'] = $value;
        }
    }

    protected function deleteImage()
    {
        if (File::exists(config('cms.image.directory') . '/' . $this->image)) {
            File::delete(config('cms.image.directory') . '/' . $this->image);
            $ext = substr(strrchr($this->image, '.'), 1);
            $thumbnail = str_replace(".{$ext}", "_thumb.{$ext}", $this->image);
            File::delete(config('cms.image.directory') . '/' . $thumbnail);
        }
    }

    public function forceDelete()
    {
        $this->deleteImage();
        return parent::forceDelete(); // TODO: Change the autogenerated stub
    }

    public function getImageUrlAttribute()
    {

        $imageUrl = '';

        if (!is_null($this->image)) {
            if (file_exists(public_path() . '/' . config('cms.image.directory') . '/' . $this->image)) {
                $imageUrl = asset(config('cms.image.directory') . '/' . $this->image);
            }
        }

        return $imageUrl;
    }

    public function getImageThumbUrlAttribute()
    {

        $imageUrl = '';

        if (!is_null($this->image)) {
            $ext = substr(strrchr($this->image, '.'), 1);
            $thumbnail = str_replace(".{$ext}", "_thumb.{$ext}", $this->image);
            if (file_exists(public_path() . '/' . config('cms.image.directory') . '/' . $thumbnail)) {
                $imageUrl = asset(config('cms.image.directory') . '/' . $thumbnail);
            }
        }

        return $imageUrl;
    }

    public function getDateAttribute()
    {
        return is_null($this->published_at) ? '' : $this->published_at->diffForHumans();
    }

    public function getBodyHtmlAttribute()
    {
        return $this->body ? Markdown::convertToHtml(e($this->body)) : null;
    }

    public function getExcerptHtmlAttribute()
    {
        return $this->excerpt ? Markdown::convertToHtml(e($this->excerpt)) : null;
    }

    public function getTagsHtmlAttribute()
    {
        $tags = [];
        foreach ($this->tags as $tag) {
            $route = route('tag', $tag->slug);
            $tags[] = "<a href=\"{$route}\">{$tag->name}</a>";
        }
        return implode(', ', $tags);
    }

    public function getCommentCountAttribute()
    {
        $commentsCount = $this->comments->count();
        return $commentsCount . ' ' . Str::plural('Comment', $commentsCount);
    }

    public function getAuthorPostCountAttribute()
    {
        $authorPostCount = $this->author->posts()->published()->count();
        return $authorPostCount . ' ' . Str::plural('post', $authorPostCount);
    }

    public function postTags($implode = true)
    {
        $tags = $this->tags->pluck('name')->toArray();
        if ($implode) return implode(', ', $tags);
        return $tags;
    }

    public function dateFormatted($showTimes = false)
    {
        $format = 'd/m/Y';
        if ($showTimes) $format .= ' H:i:s';
        return $this->created_at->format($format);
    }

    public function deletionDateFormatted()
    {
        return $this->deleted_at->format('d/m/Y');
    }

    public function publicationLabel()
    {
        if (!$this->published_at) return '<span class="label label-warning">Draft</span>';
        elseif ($this->published_at && $this->published_at->isFuture()) return '<span class="label label-info">Scheduled</span>';
        else return '<span class="label label-success">Published</span>';
    }

    public function scopeLatestFirst($query)
    {
        //can be used latest() built-in function
        return $query->orderBy('published_at', 'desc');
    }

    public function scopePopular($query)
    {
        return $query->orderBy('view_count', 'desc');
    }

    public function scopePublished($query)
    {
        return $query->where('published_at', '<=', Carbon::now());
    }

    public function scopeScheduled($query)
    {
        return $query->where('published_at', '>', Carbon::now());
    }

    public function scopeDraft($query)
    {
        return $query->whereNull('published_at');
    }

    public function scopeArchives($query)
    {
        return $query->selectRaw('COUNT(id) AS post_count, YEAR(published_at) as year, MONTHNAME(published_at) as month')->published()->groupBy('year', 'month')->orderByRaw('min(published_at) desc');
    }

    public function scopeFilter($query, $filter)
    {
        if (isset($filter['month']) && $month = $filter['month']) {
            $query->whereMonth('published_at', Carbon::parse($month)->month);
        }
        if (isset($filter['year']) && $year = $filter['year']) {
            $query->whereYear('published_at', $year);
        }
        if (isset($filter['term']) && $term = $filter['term']) {
            $query->where(function ($q) use ($term) {
                $q->orWhere('title', 'LIKE', "%{$term}%");
                $q->orWhere('excerpt', 'LIKE', "%{$term}%");
                $q->orWhere('body', 'LIKE', "%{$term}%");
                $q->orWhereHas('author', function ($subQuery) use ($term) {
                    $subQuery->where('name', 'LIKE', "%{$term}%");
                });
                $q->orWhereHas('category', function ($subQuery) use ($term) {
                    $subQuery->where('title', 'LIKE', "%{$term}%");
                });
            });
        }
        return $query;
    }

    public function attachTags($tags)
    {
        $tags = explode(",", $tags);
        $tagIds = [];

        foreach ($tags as $tag) {
            $newTag = Tag::firstOrCreate([
                'name' => ucwords(trim($tag)),
                'slug' => Str::slug($tag)
            ]);

            $tagIds[] = $newTag->id;
        }

        $this->tags()->sync($tagIds);
    }

    public function ownerKey($owner)
    {
        return $this->author_id;
    }
}
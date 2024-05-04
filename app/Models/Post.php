<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Post extends Model
{
    use HasFactory;


    protected $fillable = [
        'message',
        'replying_to',
        'image',
        'image_path'
    ];

    protected $hidden=[
    ];

    protected $appends=[
        'is_like',
        'is_rePost',
        'likes_count',
        'rePosts_count',
        'replies_count',
        'Poster',
        'rePosted_Post',
    ];


    public function user():BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function likes():BelongsToMany
    {
        return $this
            ->belongsToMany(User::class, 'Post_likes', 'Post_id', 'user_id')
            ->withTimestamps();
    }



    public function replies():HasMany
    {
        return $this->hasMany(Post::class, 'replying_to');

    }




    public function repliesCount():Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                if(isset($attributes['rePosting'])){
                    $Post = Post::find($attributes['rePosting']);
                    return $Post->replies()->count();
                }
                return $this->replies()->count();
            }
        );
    }



    public function rePosts():HasMany
    {
        return $this->hasMany(Post::class, 'rePosting');

    }

    public function originalPost():BelongsTo
    {
        return $this->belongsTo(Post::class, 'rePosting');
    }



    public function scopeIsReply(Builder $query, $reply=true): Builder
    {
        if($reply){
            return $query->whereNotNull('replying_to');
        }else{
            return $query->whereNull('replying_to');
        }

    }

    public function inReplyTo():BelongsTo
    {
        return $this->belongsTo(Post::class, 'replying_to');
    }

    public function isLike(): Attribute
    {

        return Attribute::make(
            get: function ($value, $attributes) {
                if(isset($attributes['rePosting'])){
                    $Post = Post::find($attributes['rePosting']);
                    return $Post->likes()->where('user_id', auth()->id())->exists();
                }
                return $this->likes()->where('user_id', auth()->id())->exists();
            },
            set: function ($value) {
                if($value){
                    $this->likes()->attach(auth()->id());
                }else{
                    $this->likes()->detach(auth()->id());
                }
            }
        );

    }

    public function likesCount():Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                if(isset($attributes['rePosting'])){
                    $Post = Post::find($attributes['rePosting']);
                    return $Post->likes()->count();
                }
                return $this->likes()->count();
            }
        );
    }

    public function isRePost():Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                if(isset($attributes['rePosting'])){
                    $Post = Post::find($attributes['rePosting']);
                    return $Post->rePosts()->where('user_id', auth()->id())->exists();
                }
                return $this->rePosts()->where('user_id', auth()->id())->exists();
            }
        );

    }

    public function rePostsCount():Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                if(isset($attributes['rePosting'])){
                    $Post = Post::find($attributes['rePosting']);
                    return $Post->rePosts()->count();
                }
                return $this->rePosts()->count();
            }
        );
    }

    public function Poster():Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                return $this->user;
            }
        );
    }

    public function rePostedPost():Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                if(isset($attributes['rePosting'])){
                    return Post::find($attributes['rePosting']);
                }
                return null;
            }
        );
    }

}

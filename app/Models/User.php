<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'bio'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = [
        'is_following',
        'followers_count',
        'following_count'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function Posts(): HasMany
    {
        return $this->hasMany(Post::class)->latest();
    }

    public function posts():HasMany
    {
        return $this
            ->hasMany(Post::class)
            ->whereNull('replying_to')
            ->latest();
    }

    public function replies()
    {
        return $this->Posts()->whereNotNull('replying_to');

    }

    public function likedPosts():hasMany
    {
        return $this->hasMany(PostLikes::class )->latest();
    }

    public function followers():BelongsToMany
    {
        return $this->belongsToMany(User::class, 'followers', 'user_id', 'follower_id')
            ->withTimestamps();
    }

    public function followersCount():Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                return $this->followers()->count();
            }
        );
    }

    public function following():BelongsToMany
    {
        return $this->belongsToMany(User::class, 'followers', 'follower_id', 'user_id');
    }

    public function followingCount():Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                return $this->following()->count();
            }
        );
    }

    public function isFollowing():Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                return $this->followers()->where('follower_id', auth()->id())->exists();
            }
        );

    }

    public function isFollowedBy(string $id):bool
    {
        return $this->followers()->where('follower_id', $id)->exists();
    }


}

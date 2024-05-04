<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PostLikes extends Model
{
    use HasFactory;

    function Post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}

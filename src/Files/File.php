<?php

namespace Laravelista\Comments\Files;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Config;

class File extends Model
{
	use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'comment_id', 'name', 'url', 'type',
        'additional_key', 'file_size', 'status', 'tenant_id',
    ];

    /**
     * The file may or may not be attached to a comment, based on the comment_id
     */
    public function comment(): BelongsTo
    {
        return $this->belongsTo(Config::get('comments.model'), 'comment_id');
    }
}

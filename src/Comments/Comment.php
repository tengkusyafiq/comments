<?php

namespace Laravelista\Comments\Comments;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Config;
use Laravelista\Comments\Comments\Events\CommentCreated;
use Laravelista\Comments\Comments\Events\CommentDeleted;
use Laravelista\Comments\Comments\Events\CommentUpdated;
use Laravelista\Comments\Files\File;

class Comment extends Model
{
	use SoftDeletes;

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = [
        'commenter'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'comment', 'approved', 'guest_name', 'guest_email',
        'tenant_id', // for multi tenant and indexing.
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'approved' => 'boolean'
    ];

    /**
     * The event map for the model.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => CommentCreated::class,
        'updated' => CommentUpdated::class,
        'deleted' => CommentDeleted::class, // TODO: also delete files in the files table and file manager.
    ];

    /**
     * The user who posted the comment.
     */
    public function commenter()
    {
        return $this->morphTo();
    }

    /**
     * The model that was commented upon.
     */
    public function commentable()
    {
        return $this->morphTo();
    }

    /**
     * Returns all comments that this comment is the parent of.
     */
    public function children()
    {
        return $this->hasMany(Config::get('comments.model'), 'child_id');
    }

    /**
     * Returns the comment to which this comment belongs to.
     */
    public function parent()
    {
        return $this->belongsTo(Config::get('comments.model'), 'child_id');
    }

    /**
     * Returns all files that this comment is the parent of.
     */
    public function files(): HasMany
    {
        return $this->hasMany(File::class, 'comment_id');
    }
}

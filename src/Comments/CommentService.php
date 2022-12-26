<?php

namespace Laravelista\Comments\Comments;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Laravelista\Comments\Files\FileService;

class CommentService
{
    /**
     * Handles creating a new comment for given model.
     * @return mixed the configured comment-model
     * @throws ValidationException
     */
    public function store(Request $request): mixed
    {
        Validator::make($request->all(), [
            'message' => ['required', 'string'],
            'commentable_id' => ['required', 'string', 'min:1'],
            'commentable_type' => ['required', 'string'],
        ])->validate();

        $model = $request->commentable_type::findOrFail($request->commentable_id);

        $commentClass = Config::get('comments.model');
        $comment = new $commentClass;

        $comment->commenter()->associate(Auth::user());

        $comment->commentable()->associate($model);
        $comment->comment = $request->message;
        $comment->approved = !Config::get('comments.approval_required');
        $comment->save();

        // save files
        if ($request->has('files')) {
            $comment = (new FileService)->store($request, $comment);
        }

        return $comment;
    }

    /**
     * Handles updating the message of the comment.
     * @return mixed the configured comment-model
     * @throws ValidationException
     */
    public function update(Request $request, Comment $comment): mixed
    {
        Gate::authorize('edit-comment', $comment);

        Validator::make($request->all(), [
            'message' => ['required', 'string'],
        ])->validate();

        $comment->update([
            'comment' => $request->message
        ]);

        // update files
        if ($request->has('store_files') || $request->has('destroy_files')) {
            $comment = (new FileService)->update($request, $comment);
        }

        return $comment;
    }

    /**
     * Handles deleting a comment.
     * @param Comment $comment
     * @return void the configured comment-model
     */
    public function destroy(Comment $comment): void
    {
        Gate::authorize('delete-comment', $comment);

        // delete files
        if ($files = $comment->files) {
            (new FileService)->destroyAll($comment->files);
        }

        if (Config::get('comments.soft_deletes') == true) {
            $comment->delete();
        } else {
            $comment->forceDelete();
        }
    }

    /**
     * Handles creating a reply "comment" to a comment.
     * @return mixed the configured comment-model
     * @throws ValidationException
     */
    public function reply(Request $request, Comment $comment): mixed
    {
        Gate::authorize('reply-to-comment', $comment);

        Validator::make($request->all(), [
            'message' => ['required', 'string'],
        ])->validate();

        $commentClass = Config::get('comments.model');
        $reply = new $commentClass;
        $reply->commenter()->associate(Auth::user());
        $reply->commentable()->associate($comment->commentable);
        $reply->parent()->associate($comment);
        $reply->comment = $request->message;
        $reply->approved = !Config::get('comments.approval_required');
        $reply->save();

        // save files
        if ($request->has('files')) {
            $reply = (new FileService)->store($request, $reply);
        }

        return $reply;
    }
}

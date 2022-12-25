<?php

namespace Laravelista\Comments\Comments;

use Laravelista\Comments\Comments;

class CommentPolicy
{
    /**
     * Can user create the comment
     *
     * @param $user
     * @return bool
     */
    public function create($user) : bool
    {
        return true;
    }

    /**
     * Can user delete the comment
     *
     * @param $user
     * @param \Laravelista\Comments\Comments\Comment $comment
     * @return bool
     */
    public function delete($user, Comments\Comment $comment) : bool
    {
        return $user->getKey() == $comment->commenter_id;
    }

    /**
     * Can user update the comment
     *
     * @param $user
     * @param \Laravelista\Comments\Comments\Comment $comment
     * @return bool
     */
    public function update($user, Comments\Comment $comment) : bool
    {
        return $user->getKey() == $comment->commenter_id;
    }

    /**
     * Can user reply to the comment
     *
     * @param $user
     * @param \Laravelista\Comments\Comments\Comment $comment
     * @return bool
     */
    public function reply($user, Comments\Comment $comment) : bool
    {
        return $user->getKey() != $comment->commenter_id;
    }
}


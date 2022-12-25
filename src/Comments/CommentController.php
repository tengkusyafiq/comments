<?php

namespace Laravelista\Comments\Comments;

use Illuminate\Routing\Controller;

abstract class CommentController extends Controller implements CommentControllerInterface
{
    public function __construct()
    {
//        $this->middleware('web');
//
//        if (Config::get('comments.guest_commenting') == true) {
//            $this->middleware('auth')->except('store');
//            $this->middleware(ProtectAgainstSpam::class)->only('store');
//        } else {
//            $this->middleware('auth');
//        }
    }
}
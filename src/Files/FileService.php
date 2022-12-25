<?php

namespace Laravelista\Comments\Files;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Laravelista\Comments\Comments\Comment;

class FileService
{
    /**
     * Handles creating a new file for given model.
     * @return Comment the configured comment-model
     * @throws ValidationException
     */
    public function store(Request $request, Comment $comment): Comment
    {
        Validator::make($request->all(), [
            'files' => ['nullable', 'array'],
            // files validation
            'files.*' => ['array'],
            // should follow File.php validation rules
            'files.*.name' => ['required', 'string', 'max:255'],
            'files.*.type' => ['required', 'string', 'max:255'],
            'files.*.size' => ['required', 'integer'],
            'files.*.url' => ['required', 'string'],
            'files.*.status' => ['required', 'integer'],
            'files.*.additional_key' => ['nullable', 'string'],
        ])->validate();

        // example of json request
        // {
        //     "message": "test",
        //     "files": [
        //         {
        //             "name": "test.jpg",
        //             "type": "image/jpeg",
        //             "size": 123456,
        //             "url": "XVPYGRWm/goal40/2021/11/18/20211118_154843_example.jpeg",
        //             "status": 1,
        //             "additional_key": "test"
        //         }
        //     ]
        // }

        // save files
        if ($request->has('files')) {
            $comment->files()->createMany($request->only('files'));
        }

        return $comment;
    }


    /**
     * Handles updating the files of the comment.
     * @throws ValidationException
     */
    public function update(Request $request, Comment $comment): Comment
    {
        Gate::authorize('edit-comment', $comment);

        Validator::make($request->all(), [
            // store files validation
            'store_files' => ['nullable', 'array'],
            'store_files.*' => ['array'],
            // should follow File.php validation rules
            'store_files.*.name' => ['required', 'string', 'max:255'],
            'store_files.*.type' => ['required', 'string', 'max:255'],
            'store_files.*.size' => ['required', 'integer'],
            'store_files.*.url' => ['required', 'string'],
            'store_files.*.status' => ['required', 'integer'],
            'store_files.*.additional_key' => ['nullable', 'string'],
            // destroy files validation
            'destroy_files' => ['nullable', 'array'], // array of file ids
            'destroy_files.*' => ['integer'],
        ])->validate();

        // example of the json request
        // {
        //     "message": "updated test",
        //     "store_files": [
        //         {
        //             "name": "file1.jpg",
        //             "type": "image/jpeg",
        //             "size": 123456,
        //             "url": "http://localhost:8000/storage/file1.jpg",
        //             "status": 1,
        //             "additional_key": "value"
        //         }
        //     ],
        //     "destroy_files": [1, 2, 3]
        // }

        // save new files
        if ($request->has('store_files')) {
            $comment->files()->createMany($request->only('store_files'));
        }

        // destroy files
        if ($request->has('destroy_files')) {
            $comment->files()->whereIn('id', $request->only('destroy_files'))->delete();
        }

        return $comment;
    }

    /**
     * Handles deleting a file.
     */
    public function destroy(File $file): ?bool
    {
        Gate::authorize('edit-comment', $file);
        try {
            // TODO: to connect to vimigo file manager service and request delete the file asset
        } catch (\Exception $e) {
            return false;
        }
        return $file->delete();
    }

    /**
     * Destroy all files of a comment.
     * @param Collection|null $files
     * @return bool|null
     */
    public function destroyAll(?Collection $files): ?bool
    {
        foreach ($files as $file) {
            $this->destroy($file);
        }
        return true;
    }

}

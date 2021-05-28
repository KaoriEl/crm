<?php

namespace App\Http\Controllers\Api;

use App\Models\Post;
use MediaUploader;
use App\Http\Controllers\Controller;
use Plank\Mediable\Exceptions\MediaUpload\ConfigurationException;
use Plank\Mediable\Exceptions\MediaUpload\FileExistsException;
use Plank\Mediable\Exceptions\MediaUpload\FileNotFoundException;
use Plank\Mediable\Exceptions\MediaUpload\FileNotSupportedException;
use Plank\Mediable\Exceptions\MediaUpload\FileSizeException;
use Plank\Mediable\Exceptions\MediaUpload\ForbiddenException;

/**
 * Отвечает за работу с прикрепленными файлами поста.
 */
class PostFileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Прикрепляет файл к посту.
     *
     * @param Post $post
     * @return array
     * @throws ConfigurationException
     * @throws FileExistsException
     * @throws FileNotFoundException
     * @throws FileNotSupportedException
     * @throws FileSizeException
     * @throws ForbiddenException
     */
    public function store(Post $post)
    {
        $file = request()->file('file');

        $media = MediaUploader::fromSource($file)
            ->useHashForFilename()
            ->toDestination('public', 'uploads')
            ->upload();

        $post->attachMedia($media, 'attachment');

        return $media->prepareForResponse();
    }

    /**
     * Открепляет файл от поста и удаляет файл.
     *
     * @param Post $post
     * @return string
     */
    public function destroy(Post $post)
    {
        $validated = request()->validate([
            'id' => 'required|exists:media',
        ]);

        $post->detachMedia($validated['id']);

        return response('');
    }
}

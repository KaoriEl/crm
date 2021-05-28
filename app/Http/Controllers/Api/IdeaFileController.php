<?php

namespace App\Http\Controllers\Api;

use App\Models\Idea;
use Illuminate\Auth\Access\AuthorizationException;
use MediaUploader;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Plank\Mediable\Exceptions\MediaUpload\ConfigurationException;
use Plank\Mediable\Exceptions\MediaUpload\FileExistsException;
use Plank\Mediable\Exceptions\MediaUpload\FileNotFoundException;
use Plank\Mediable\Exceptions\MediaUpload\FileNotSupportedException;
use Plank\Mediable\Exceptions\MediaUpload\FileSizeException;
use Plank\Mediable\Exceptions\MediaUpload\ForbiddenException;

class IdeaFileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Прикрепляет файл к идее.
     *
     * @param Idea $idea
     * @return Response
     * @throws AuthorizationException
     * @throws ConfigurationException
     * @throws FileExistsException
     * @throws FileNotFoundException
     * @throws FileNotSupportedException
     * @throws FileSizeException
     * @throws ForbiddenException
     */
    public function store(Idea $idea)
    {
        $this->authorize('update', $idea);

        request()->validate([
            'file' => 'required|file',
        ]);

        $file = request()->file('file');

        $media = MediaUploader::fromSource($file)
            ->useHashForFilename()
            ->toDestination('public', 'uploads')
            ->upload();

        $idea->attachMedia($media, 'attachment');

        return $media->prepareForResponse();
    }

    /**
     * Открепляет файл от идеи.
     *
     * @param Idea $idea
     * @return Response
     * @throws AuthorizationException
     */
    public function destroy(Idea $idea)
    {
        $this->authorize('update', $idea);

        $validated = request()->validate([
            'id' => 'required|exists:media',
        ]);

        $idea->detachMedia($validated['id'], 'attachment');

        return new Response();
    }
}

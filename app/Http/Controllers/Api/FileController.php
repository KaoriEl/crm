<?php

namespace App\Http\Controllers\Api;

use App\Models\Idea;
use App\Models\Post;
use App\Models\Media;
use Illuminate\Http\Response;
use MediaUploader;
use App\Http\Controllers\Controller;
use Plank\Mediable\Exceptions\MediaUpload\ConfigurationException;
use Plank\Mediable\Exceptions\MediaUpload\FileExistsException;
use Plank\Mediable\Exceptions\MediaUpload\FileNotFoundException;
use Plank\Mediable\Exceptions\MediaUpload\FileNotSupportedException;
use Plank\Mediable\Exceptions\MediaUpload\FileSizeException;
use Plank\Mediable\Exceptions\MediaUpload\ForbiddenException;

class FileController extends Controller
{
    public function __construct()
    {

    }

    /**
     * Загружает временный файл и возвращает информацию о файле.
     *
     * @return Response
     * @throws ConfigurationException
     * @throws FileExistsException
     * @throws FileNotFoundException
     * @throws FileNotSupportedException
     * @throws FileSizeException
     * @throws ForbiddenException
     */
    public function store()
    {
        $validated = request()->validate([
            'file' => 'required|file',
        ]);

        $file = request()->file('file');

        $media = MediaUploader::fromSource($file)
            ->useHashForFilename()
            ->toDestination('public', 'uploads')
            ->upload();

        return $media->prepareForResponse();
    }

    /**
     * Удаляет временный файл.
     *
     * @return Response
     */
    public function destroy()
    {
        $validated = request()->validate([
            'id' => 'required|exists:media',
        ]);

        $media = Media::findOrFail($validated['id']);

        $media->delete();

        return response('');
    }
}

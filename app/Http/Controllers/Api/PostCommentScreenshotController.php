<?php

namespace App\Http\Controllers\Api;

use App\Models\Post;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

class PostCommentScreenshotController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Открепляет скриншот комментария от поста.
     *
     * @param  Post $post
     * @return Response
     */
    public function destroy(Post $post)
    {
        if ($post->hasCommentScreenshot()) {
            $post->detachMedia($post->getMedia('comment_screenshot'));
        }

        return response('');
    }

    /**
     * Открепляет скриншот комментария VK от поста.
     *
     * @param  Post $post
     * @return Response
     */
    public function vkdestroy(Post $post)
    {
        if ($post->hasCommentScreenshotVK()) {
            $post->detachMedia($post->getMedia('vk_screenshot'));
        }

        return response('');
    }
    /**
     * Открепляет скриншот комментария OK от поста.
     *
     * @param  Post $post
     * @return Response
     */
    public function okdestroy(Post $post)
    {
        if ($post->hasCommentScreenshotOK()) {
            $post->detachMedia($post->getMedia('ok_screenshot'));
        }

        return response('');
    }
    /**
     * Открепляет скриншот комментария FB от поста.
     *
     * @param  Post $post
     * @return Response
     */
    public function fbdestroy(Post $post)
    {
        if ($post->hasCommentScreenshotFB()) {
            $post->detachMedia($post->getMedia('fb_screenshot'));
        }

        return response('');
    }
    /**
     * Открепляет скриншот комментария VK от поста.
     *
     * @param  Post $post
     * @return Response
     */
    public function instadestroy(Post $post)
    {
        if ($post->hasCommentScreenshotINSTA()) {
            $post->detachMedia($post->getMedia('insta_screenshot'));
        }

        return response('');
    }
}

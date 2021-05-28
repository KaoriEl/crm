<?php

namespace App\Http\Requests\Post;

use App\Http\Requests\Project\PutProjectRequest;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'smm_links' => 'nullable|array|min:1',
            'seed_links' => 'nullable|array|min:1',
            'vk_post_url' => 'nullable|url',
            'tt_post_url' => 'nullable|url',
            'ok_post_url' => 'nullable|url',
            'fb_post_url' => 'nullable|url',
            'ig_post_url' => 'nullable|url',
            'y_dzen_post_url' => 'nullable|url',
            'y_street_post_url' => 'nullable|url',
            'yt_post_url' => 'nullable|url',
            'tg_post_url' => 'nullable|url',

//            'targeted_to.*.status' => 'nullable|required',
            'journalist_id' => 'integer',

            'seed_list_url' => 'nullable|url',

            'commented' => 'boolean',
        ];
    }


}

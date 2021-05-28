<?php

namespace App\Http\Requests\Post;

use App\Idea;
use Carbon\Carbon;


use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class StorePost extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::user()->hasRole('editor');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required',
            'text' => 'required',

            'journalist_id' => 'nullable|exists:users,id',

            'posting' => 'nullable|boolean',
            'commercial_seed' => 'nullable|boolean',
            'posting_to_vk' => 'nullable|boolean',
            'posting_to_ok' => 'nullable|boolean',
            'posting_to_fb' => 'nullable|boolean',
            'posting_to_ig' => 'nullable|boolean',
            'posting_to_y_dzen' => 'nullable|boolean',
            'posting_to_y_street' => 'nullable|boolean',
            'posting_to_yt' => 'nullable|boolean',
            'posting_to_tt' => 'nullable|boolean',
            'posting_to_tg' => 'nullable|boolean',
            'commercial_seed_to_vk' => 'nullable|boolean',
            'commercial_seed_to_ok' => 'nullable|boolean',
            'commercial_seed_to_fb' => 'nullable|boolean',
            'commercial_seed_to_ig' => 'nullable|boolean',
            'commercial_seed_to_y_dzen' => 'nullable|boolean',
            'commercial_seed_to_y_street' => 'nullable|boolean',
            'commercial_seed_to_yt' => 'nullable|boolean',
            'commercial_seed_to_tt' => 'nullable|boolean',
            'commercial_seed_to_tg' => 'nullable|boolean',
            'posting_text' => 'nullable',
            'commercial_seed_text' => 'nullable',

            'targeting' => 'nullable|boolean',
            'targeting_text' => 'nullable',

            'seeding' => 'nullable|boolean',
            'seeding_to_vk' => 'nullable|boolean',
            'seeding_to_ok' => 'nullable|boolean',
            'seeding_to_fb' => 'nullable|boolean',
            'seeding_to_insta' => 'nullable|boolean',
            'seeding_to_y_dzen' => 'nullable|boolean',
            'seeding_to_y_street' => 'nullable|boolean',
            'seeding_to_yt' => 'nullable|boolean',
            'seeding_to_tg' => 'nullable|boolean',
            'seeding_text' => 'nullable',

            'commenting' => 'nullable|boolean',
            'commenting_text' => 'nullable',
            'project_id' => 'nullable',
        ];
    }

    /**
     * Дата истечения срока поста.
     *
     *
     * @return Carbon
     */
    public function expiresAt()
    {
        // переведем дату из часового пояса пользователя в часовой пояс приложения (UTC по-умолчанию)
        return Carbon::createFromFormat('d.m.Y H:i', $this->get('expires_at'), Auth::user()->timezone)
            ->setTimezone(config('app.timezone'));
    }
}

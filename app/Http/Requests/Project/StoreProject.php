<?php

namespace App\Http\Requests\Project;

use App\Idea;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class StoreProject extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::user()->hasRole('editor') || Auth::user()->hasRole('admin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required',
            'description' => 'nullable|min:1',
            'site' => 'nullable|min:1',
            'publication_rate' => 'required|min:0',
            'vk' => 'nullable|min:1',
            'ok' => 'nullable|min:1',
            'fb' => 'nullable|min:1',
            'insta' => 'nullable|min:1',
            'y_street' => 'nullable|min:1',
            'y_dzen' =>'nullable|min:1',
        ];
    }

}

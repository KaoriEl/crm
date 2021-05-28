<?php

namespace App\Http\Requests\Project;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class PutProjectRequest extends FormRequest
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
            'name' => 'required|min:1',
            'description' => 'nullable|min:1',
            'site' => 'nullable|url|min:1',
            'vk' => 'nullable|url|min:1',
            'ok' => 'nullable|url|min:1',
            'fb' => 'nullable|url|min:1',
            'insta' => 'nullable|url|min:1',
            'y_street' => 'nullable|url|min:1',
            'y_dzen' =>'nullable|url|min:1',
        ];
    }
}

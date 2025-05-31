<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                'regex:/^[a-zA-Z0-9._%+-]+@navotaspolytechniccollege\.edu\.ph$/',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
        ];

        // If using base64 data, validate that instead of the file upload
        if ($this->has('using_base64') && $this->has('profile_picture_base64')) {
            $rules['profile_picture_base64'] = ['required', 'string'];
        } else {
            $rules['profile_picture'] = ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:5120'];
        }

        return $rules;
    }
}

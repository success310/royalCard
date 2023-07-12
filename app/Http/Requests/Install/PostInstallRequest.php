<?php

namespace App\Http\Requests\Install;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class PostInstallRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'APP_NAME' => 'required|max:64',
            'APP_LOGO' => 'nullable',
            'APP_LOGO_DARK' => 'nullable',
            'ADMIN_MAIL' => 'required|max:64',
            'ADMIN_PASS' => 'required|min:8|max:64|required_with:ADMIN_PASS_CONFIRM|same:ADMIN_PASS_CONFIRM',
            'ADMIN_PASS_CONFIRM' => 'required|min:8|max:64',
            'ADMIN_TIMEZONE' => 'required|timezone',
            'MAIL_FROM_ADDRESS' => 'required',
            'MAIL_FROM_NAME' => 'required',
            'MAIL_MAILER' => 'required',
            'MAIL_HOST' => 'nullable',
            'MAIL_PORT' => 'nullable|numeric',
            'MAIL_ENCRYPTION' => 'nullable',
            'MAIL_USERNAME' => 'nullable',
            'MAIL_PASSWORD' => 'nullable',
            'DB_CONNECTION' => 'nullable',
            'DB_HOST' => 'nullable',
            'DB_PORT' => 'nullable',
            'DB_DATABASE' => 'nullable',
            'DB_USERNAME' => 'nullable',
            'DB_PASSWORD' => 'nullable',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'APP_NAME' => 'app name',
            'ADMIN_PASS' => 'admin password',
            'ADMIN_PASS_CONFIRM' => 'admin password confirmation',
        ];
    }

    /**
     * Return json response.
     *
     * @return json<string, mixed>
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}

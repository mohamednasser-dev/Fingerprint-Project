<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

class UsersUpdateRequest extends FormRequest
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
            'id' => 'required|exists:users,id',
            'name' => 'required',
            'phone' => 'required|unique:users,phone,' . $this->id,
            'email' => 'required|email|unique:users,email,' . $this->id,
            'password' => 'nullable|min:6',
            'is_active' => 'required|in:0,1',
            'type' => 'required|in:user,admin',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $error = implode('- ', $validator->errors()->all());
        throw new HttpResponseException(
            sendResponse(JsonResponse::HTTP_UNPROCESSABLE_ENTITY, $error)
        );
    }
}

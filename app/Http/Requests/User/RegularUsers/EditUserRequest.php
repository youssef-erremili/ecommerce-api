<?php

namespace App\Http\Requests\User\RegularUsers;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class EditUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'min:2', 'max:25'],
            'last_name' => ['required', 'string', 'min:2', 'max:25'],
            'email' => ['required', 'email', 'unique:users,email'],
            'physical_address' => ['required', 'string', 'max:255'],
            'phone_number' => ['required', 'string', 'min:10', 'max:15'],
        ];
    }
}

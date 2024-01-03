<?php

declare(strict_types=1);

namespace App\Request;

use Hyperf\Validation\Request\FormRequest;

class SignUpRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'code' => 'required|verifyCode', // 优先校验验证码
            'email' => 'required|email|unique:users,email', // 邮箱唯一
            'password' => 'required|string|confirmed',
            'password_confirmation' => 'required|string|same:password',
        ];
    }
}

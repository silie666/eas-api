<?php

namespace App\Http\Requests\CommonApi\Auth;


use App\Http\Requests\CommonApi\Request;
use Illuminate\Validation\Rule;

class AuthRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'username'   => 'required|string',
            'password'   => 'required|string',
            'guard_type' => 'required|silie_int:tiny|' . Rule::in(cons('system.guard_type')),
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
            'username'   => '用户',
            'password'   => '密码',
            'guard_type' => '登录平台',
        ];
    }
}

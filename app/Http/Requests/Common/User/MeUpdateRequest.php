<?php

namespace App\Http\Requests\Common\User;

use App\Http\Requests\Request;
use Illuminate\Validation\Rule;

class MeUpdateRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'nullable|string|max:64',
            'sex'  => 'nullable|tc_int:tiny|' . Rule::in(cons('user.sex')),
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
            'name' => '用户姓名',
            'sex'  => '性别',
        ];
    }
}

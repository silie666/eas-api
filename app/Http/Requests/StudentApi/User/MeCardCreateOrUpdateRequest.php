<?php

namespace App\Http\Requests\StudentApi\User;

use App\Http\Requests\Request;

class MeCardCreateOrUpdateRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'brand_name'      => 'required|string',
            'number'          => 'required|string',
            'expiration_date' => 'required|' . $this->dateTimeRule,
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
            'brand_name'      => '品牌名称',
            'number'          => '卡号',
            'expiration_date' => '过期时间',
        ];
    }
}

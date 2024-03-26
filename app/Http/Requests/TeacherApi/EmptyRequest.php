<?php

namespace App\Http\Requests\TeacherApi;

class EmptyRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [];
    }
}

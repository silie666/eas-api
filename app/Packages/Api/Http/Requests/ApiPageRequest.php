<?php

namespace Package\Api\Http\Requests;

abstract class ApiPageRequest extends ApiRequest
{
    /**
     * 最大分页条数
     *
     * @var int
     */
    protected $maxPerPage = 200;

    /**
     * Get the validation page rules that apply to the request.
     *
     * @return array
     */
    abstract public function pageRules(): array;

    /**
     * Get custom page attributes for validator errors.
     *
     * @return array
     */
    abstract public function pageAttributes(): array;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return array_merge([
            'page'     => 'int|min:1',
            'per_page' => 'int|min:1|max:' . $this->maxPerPage,
        ], $this->container->call([$this, 'pageRules']));
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes(): array
    {
        return array_merge([
            'page'     => '页数，默认1',
            'per_page' => '每页条数，默认15，最大' . $this->maxPerPage,
        ], $this->container->call([$this, 'pageAttributes']));
    }

    /**
     * 当前页数
     *
     * @param int $default
     *
     * @return int
     */
    public function getPage(int $default = 1): int
    {
        $page = $this->header('T-Page-CurrentPage');
        if (is_null($page)) {
            $page = $this->input('page', $default);
        }

        return (int)$page;
    }

    /**
     * 分页数
     *
     * @param int $default
     *
     * @return int
     */
    public function getPerPage(int $default = 15): int
    {
        $perPage = $this->header('T-Page-PerPage');
        if (is_null($perPage)) {
            $perPage = $this->input('per_page', $default);
        }

        return (int)$perPage;
    }
}

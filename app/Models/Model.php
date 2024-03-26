<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;


class Model extends Eloquent
{
    use SoftDeletes;

    /**
     * The name of the "created at" column.
     *
     * @var string
     */
    const CREATED_AT = 'create_time';

    /**
     * The name of the "updated at" column.
     *
     * @var string
     */
    const UPDATED_AT = 'update_time';

    /**
     * The name of the "deleted at" column.
     *
     * @var string
     */
    const DELETED_AT = 'delete_time';

    /**
     * model名称
     *
     * @var string
     */
    const MODEL_NAME = '资源';

    /**
     * Merge the array of model attributes. No checking is done.
     *
     * @param array $attributes
     * @param bool  $sync
     *
     * @return $this
     */
    public function mergeRawAttributes(array $attributes, bool $sync = false): static
    {
        foreach ($attributes as $key => $value) {
            $this->attributes[$key] = $value;
            if ($sync) {
                $this->original[$key] = $value;
            }
        }
        return $this;
    }

    /**
     * 获取当前属性值
     *
     * @return array
     */
    public function getAttributeValues(): array
    {
        $attributeValues = [];
        foreach ($this->attributes as $key => $value) {
            $attributeValues[$key] = $this->transformModelValue($key, $value);
        }
        return $attributeValues;
    }
}

<?php

namespace App\Services\Card;

use App\Models\Card\Card;
use App\Models\User\Student;
use App\Services\BaseService;
use App\Services\SqlBuildService;

class CardService extends BaseService
{
    /**
     * 信号卡查询对象
     *
     * @param array $attributes
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function query(array $attributes = [])
    {
        $query = Card::query();

        if ($studentId = \Arr::get($attributes, 'student_id')) {
            $query->where('card_table_type', Student::class)->where('card_table_id', $studentId);
        }

        $query = SqlBuildService::buildLikeQuery($query, $attributes, [
            'brand_name' => 'brand_name',
            'number'     => 'number',
        ]);

        $query->orderByDesc('id');
        return $query;
    }

    /**
     * 创建信用卡
     *
     * @param array                    $attributes
     * @param \App\Models\User\Student $student
     * @param bool                     $isProcessed
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public static function create(array $attributes, Student $student, bool $isProcessed = false)
    {
        if (!$isProcessed) {
            $attributes = self::processAttributes($attributes);
        }
        $card = $student->cards()->create($attributes);
        return $card;
    }

    /**
     * 更新信用卡
     *
     * @param array                 $attributes
     * @param \App\Models\Card\Card $card
     * @param bool                  $isProcessed
     *
     * @return \App\Models\Card\Card
     */
    public static function update(array $attributes, Card $card, bool $isProcessed = false)
    {
        if (!$isProcessed) {
            $attributes = self::processAttributes($attributes);
        }
        $card->update($attributes);
        return $card;
    }

    /**
     * 删除信用卡
     *
     * @param \App\Models\Card\Card $card
     *
     * @return void
     */
    public static function delete(Card $card)
    {
        $card->delete();
    }

    /**
     * 处理属性
     *
     * @param array $attributes
     *
     * @return array
     */
    public static function processAttributes(array $attributes)
    {
        $collect = collect($attributes);

        return $collect->all();
    }


}
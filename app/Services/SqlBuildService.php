<?php
declare(strict_types=1);

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

/**
 * 生成sql
 * Class SqlBuildService
 */
class SqlBuildService extends BaseService
{
    /**
     * 建立like搜索查询
     *
     * @param \Illuminate\Database\Eloquent\Builder $sqlQuery
     * @param array                                 $request
     * @param array                                 $parameterNames
     * @param string|null                           $relationship
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function buildLikeQuery(
        Builder $sqlQuery,
        array $request = [],
        array $parameterNames = [],
        ?string $relationship = null
    ): Builder {
        // 注意：这里要先过滤参数，不然后面whereHas在没有参数时也会关联搜索
        foreach ($parameterNames as $sqlField => $parameterName) {
            $searchValue = Arr::get($request, $parameterName);
            if (is_null($searchValue)) {
                unset($parameterNames[$sqlField]);
            }
        }
        if (empty($parameterNames)) {
            return $sqlQuery;
        }

        if (!empty($relationship)) {
            $sqlQuery->whereHas($relationship,
                function ($relationshipQuery) use ($sqlQuery, $request, $parameterNames) {
                    self::buildLikeQuery($relationshipQuery, $request, $parameterNames);
                });
            return $sqlQuery;
        }
        foreach ($parameterNames as $sqlField => $parameterName) {
            $sqlQuery->where($sqlField, 'like', '%' . $request[$parameterName] . '%');
        }
        return $sqlQuery;
    }

    /**
     * 建立相等搜索查询
     *
     * @param \Illuminate\Database\Eloquent\Builder $sqlQuery
     * @param array                                 $request
     * @param array                                 $parameterNames
     * @param string|null                           $relationship
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function buildEqualQuery(
        Builder $sqlQuery,
        array $request = [],
        array $parameterNames = [],
        ?string $relationship = null
    ): Builder {
        // 注意：这里要先过滤参数，不然后面whereHas在没有参数时也会关联搜索
        foreach ($parameterNames as $sqlField => $parameterName) {
            $searchValue = Arr::get($request, $parameterName);
            if (is_null($searchValue)) {
                unset($parameterNames[$sqlField]);
            }
        }
        if (empty($parameterNames)) {
            return $sqlQuery;
        }

        if (!empty($relationship)) {
            $sqlQuery->whereHas($relationship,
                function ($relationshipQuery) use ($sqlQuery, $request, $parameterNames) {
                    self::buildEqualQuery($relationshipQuery, $request, $parameterNames);
                });
            return $sqlQuery;
        }
        foreach ($parameterNames as $sqlField => $parameterName) {
            $sqlQuery->where($sqlField, $request[$parameterName]);
        }
        return $sqlQuery;
    }

    /**
     * 建立时间区间搜索查询
     *
     * @param \Illuminate\Database\Eloquent\Builder $sqlQuery
     * @param array                                 $request
     * @param string                                $sqlField            搜索的数据库字段
     * @param string                                $parameterNamePrefix 搜索参数前缀，统一用 xxx_start, xxx_end 做时间区间搜索
     * @param string|null                           $relationship        模型关联里搜索
     * @param bool                                  $isDateRange         是否是日期搜索（false时精确到秒时间搜索）
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function buildTimeQuery(
        Builder $sqlQuery,
        array $request = [],
        string $sqlField = 'create_time',
        string $parameterNamePrefix = 'create_time',
        ?string $relationship = null,
        bool $isDateRange = true
    ): Builder {
        // 开始时间搜索
        $searchTimeStart = Arr::get($request, $parameterNamePrefix . '_start');
        // 结束时间搜索
        $searchTimeEnd = Arr::get($request, $parameterNamePrefix . '_end');
        if (is_null($searchTimeStart) && is_null($searchTimeEnd)) {
            return $sqlQuery;
        }

        if (!empty($relationship)) {
            $sqlQuery->whereHas($relationship,
                function ($relationshipQuery) use ($sqlQuery, $request, $sqlField, $parameterNamePrefix, $isDateRange) {
                    self::buildTimeQuery($relationshipQuery, $request, $sqlField, $parameterNamePrefix, null,
                        $isDateRange);
                });
            return $sqlQuery;
        }

        if (!is_null($searchTimeStart)) {
            if (!($searchTimeStart instanceof Carbon)) {
                $searchTimeStart = Carbon::parse($searchTimeStart);
            }
            if ($isDateRange) {
                $searchTimeStart = $searchTimeStart->startOfDay();
            }
            $sqlQuery->where($sqlField, '>=', $searchTimeStart);
        }

        if (!is_null($searchTimeEnd)) {
            if (!($searchTimeEnd instanceof Carbon)) {
                $searchTimeEnd = Carbon::parse($searchTimeEnd);
            }
            if ($isDateRange) {
                $searchTimeEnd = $searchTimeEnd->endOfDay();
            }
            $sqlQuery->where($sqlField, '<=', $searchTimeEnd);
        }

        return $sqlQuery;
    }


    /**
     * 建立区间搜索查询
     *
     * @param \Illuminate\Database\Eloquent\Builder $sqlQuery
     * @param array                                 $request
     * @param string                                $sqlField            搜索的数据库字段
     * @param string                                $parameterNamePrefix 搜索参数前缀，统一用 xxx_start, xxx_end 做时间区间搜索
     * @param string|null                           $relationship        模型关联里搜索
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function buildRangeQuery(
        Builder $sqlQuery,
        array $request = [],
        string $sqlField = 'price',
        string $parameterNamePrefix = 'price',
        ?string $relationship = null,
    ): Builder {
        // 开始搜索
        $searchStart = Arr::get($request, $parameterNamePrefix . '_start');
        // 结束搜索
        $searchEnd = Arr::get($request, $parameterNamePrefix . '_end');
        if (empty($searchStart) && empty($searchEnd)) {
            return $sqlQuery;
        }

        if (!empty($relationship)) {
            $sqlQuery->whereHas($relationship,
                function ($relationshipQuery) use ($sqlQuery, $request, $sqlField, $parameterNamePrefix) {
                    self::buildRangeQuery($relationshipQuery, $request, $sqlField, $parameterNamePrefix);
                });
            return $sqlQuery;
        }

        if (!is_null($searchStart)) {
            $sqlQuery->where($sqlField, '>=', $searchStart);
        }

        if (!is_null($searchEnd)) {
            $sqlQuery->where($sqlField, '<=', $searchEnd);
        }

        return $sqlQuery;
    }

    /**
     * 建立分割字符串的In搜索查询
     *
     * @param \Illuminate\Database\Eloquent\Builder $sqlQuery
     * @param array                                 $request
     * @param array                                 $parameterNames
     * @param string|null                           $relationship
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function buildExplodeInQuery(
        Builder $sqlQuery,
        array $request = [],
        array $parameterNames = [],
        ?string $relationship = null
    ): Builder {
        // 注意：这里要先过滤参数，不然后面whereHas在没有参数时也会关联搜索
        $formatRequest = [];
        foreach ($parameterNames as $sqlField => $parameterName) {
            $searchValue = Arr::get($request, $parameterName);
            if (is_null($searchValue)) {
                unset($parameterNames[$sqlField]);
            } else {
                $searchValue = is_string($searchValue) ? explode(',', $searchValue) : $searchValue;
                $searchArray = array_unique(array_filter(array_map(function ($searchItem) {
                    return trim($searchItem);
                }, $searchValue), function ($searchItem) {
                    return !empty($searchItem);
                }));
                if (empty($searchArray)) {
                    unset($parameterNames[$sqlField]);
                }
                $formatRequest[$parameterName] = $searchArray;
            }
        }
        if (empty($parameterNames)) {
            return $sqlQuery;
        }

        if (!empty($relationship)) {
            $sqlQuery->whereHas($relationship,
                function ($relationshipQuery) use ($sqlQuery, $request, $parameterNames) {
                    self::buildExplodeInQuery($relationshipQuery, $request, $parameterNames);
                });
            return $sqlQuery;
        }

        foreach ($parameterNames as $sqlField => $parameterName) {
            $sqlQuery->whereIn($sqlField, $formatRequest[$parameterName]);
        }
        return $sqlQuery;
    }

    /**
     * 根据给定字段的长度自动匹配为相等搜索还是like搜索
     *
     * @param \Illuminate\Database\Eloquent\Builder $sqlQuery
     * @param array                                 $request
     * @param array                                 $parameterNames
     * @param array                                 $parameterNameFunctions
     * @param string|null                           $relationship
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function autoSwitchLikeOrEqual(
        Builder $sqlQuery,
        array $request = [],
        array $parameterNames = [],
        array $parameterNameFunctions = [],
        ?string $relationship = null
    ): Builder {
        $equalQuery = [];
        $likeQuery  = [];

        foreach ($parameterNameFunctions as $parameterName => $parameterNameFunction) {
            $searchValue = Arr::get($request, $parameterNames[$parameterName]);
            if (is_null($searchValue)) {
                continue;
            }
            if (mb_strlen($searchValue) >= $parameterNameFunction) {
                $equalQuery[$parameterName] = $parameterNames[$parameterName];
                continue;
            }
            $likeQuery[$parameterName] = $parameterNames[$parameterName];
        }

        if (!empty($equalQuery)) {
            $sqlQuery = self::buildEqualQuery($sqlQuery, $request, $equalQuery, $relationship);
        }
        if (!empty($likeQuery)) {
            $sqlQuery = self::buildLikeQuery($sqlQuery, $request, $likeQuery, $relationship);
        }

        return $sqlQuery;
    }

    public static function buildJsonContainsQuery(
        Builder $sqlQuery,
        array $request = [],
        array $parameterNames = [],
        ?string $relationship = null
    ): Builder {
        // 注意：这里要先过滤参数，不然后面whereHas在没有参数时也会关联搜索
        foreach ($parameterNames as $sqlField => $parameterName) {
            $searchValue = Arr::get($request, $parameterName);
            if (is_null($searchValue)) {
                unset($parameterNames[$sqlField]);
            }
        }
        if (empty($parameterNames)) {
            return $sqlQuery;
        }

        if (!empty($relationship)) {
            $sqlQuery->whereHas($relationship,
                function ($relationshipQuery) use ($sqlQuery, $request, $parameterNames) {
                    self::buildJsonContainsQuery($relationshipQuery, $request, $parameterNames);
                });
            return $sqlQuery;
        }
        foreach ($parameterNames as $sqlField => $parameterName) {
            $sqlQuery->whereJsonContains($sqlField, $request[$parameterName]);
        }
        return $sqlQuery;
    }
}
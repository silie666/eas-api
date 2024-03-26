<?php
declare(strict_types=1);

namespace App\Models;

class FSMStatus
{
    /**
     * 通用状态 状态切换控制
     *
     * @return \string[][]
     */
    public static function commonStatusTransitions(): array
    {
        return [
            'enabled'  => ['disabled' => 'enabled'],
            'disabled' => ['enabled' => 'disabled'],
        ];
    }

    public static function courceBillPayStatusTransitions()
    {
        return [
            'unpaid' => ['fail' => 'unpaid'],
            'fail'   => ['paying' => 'fail'],
            'paying' => ['unpaid' => 'paying', 'fail' => 'paying'],
            'paid'   => ['paying' => 'paid'],
        ];
    }
}
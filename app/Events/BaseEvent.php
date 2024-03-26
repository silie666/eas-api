<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;

class BaseEvent
{
    use Dispatchable;
    use SerializesModels {
        SerializesModels::__unserialize as parent_unserialize;
    }

    public $currentUser;

    public function __construct()
    {
        if (Auth::hasUser()) {
            $this->currentUser = Auth::user();
        }
    }

    /**
     * Restore the model after serialization.
     *
     * @param array $values
     *
     * @return void
     */
    public function __unserialize(array $values)
    {
        $this->parent_unserialize($values);
        if (\App::runningInConsole()) {
            //命令行下执行
            if (!is_null($this->currentUser)) {
                //恢复当前用户记录
                Auth::setUser($this->currentUser);
            }
        }
    }
}

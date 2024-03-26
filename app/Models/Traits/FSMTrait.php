<?php

declare(strict_types=1);

namespace App\Models\Traits;

use Illuminate\Support\Arr;
use Package\Exceptions\Client\StatusLockException;
use Package\Exceptions\Server\InternalServerException;
use Package\FSM\FSM;

trait FSMTrait
{
    /**
     * @var \Package\FSM\FSM[]
     */
    private $fsmObjects = [];

    /**
     * 缓存 FSM 事务转换列表
     *
     * @var array
     */
    private $fsmTransitions = [];

    /**
     * 缓存 FSM 常量列表
     *
     * @var array
     */
    private $fsmConstants = [];

    /**
     * 状态切换函数
     *
     * @param string     $symbol
     * @param array      $payload
     * @param string     $currentState
     * @param string     $nextState
     * @param array|null $extends
     *
     * @return bool
     */
    public static function fsmChangeState(
        string $symbol,
        array &$payload,
        string $currentState,
        string $nextState,
        ?array $extends
    ): bool {
        /* @var static $model */
        [$model, $statusKey] = $payload;

        $attributes = [$statusKey => $model->fsmStateValueForKey($statusKey, $nextState)];
        if (is_array($extends)) {
            $attributes = array_merge($extends, $attributes);
        }

        if (!static::where('id', $model->id)
            ->where($statusKey, $model->fsmStateValueForKey($statusKey, $currentState))
            ->update($attributes)) {
            throw new StatusLockException(__CLASS__ . " 状态切换异常: 操作指令 = {$symbol}, ID = {$model->id}, {$currentState} => {$nextState}");
        }

        $model->setRawAttributes(array_merge($model->getAttributes(), $attributes), true);
        return true;
    }

    /**
     * 根据状态key获取value
     *
     * @param string $statusKey
     * @param string $stateKey
     *
     * @return int
     */
    public function fsmStateValueForKey(string $statusKey, string $stateKey): int
    {
        return cons($this->fsmConstants[$statusKey] . '.' . $stateKey);
    }

    /**
     * FSM 当前状态
     *
     * @param string $statusKey
     * @param string $symbol
     *
     * @return bool
     */
    public function fsmIs(string $statusKey, string $symbol): bool
    {
        return $this->fsmObject($statusKey)->getCurrentState() === $symbol;
    }

    /**
     * 获取单个 FSM 对象
     *
     * @param string $statusKey
     *
     * @return \Package\FSM\FSM
     */
    protected function fsmObject(string $statusKey): FSM
    {
        $fsmObject = Arr::get($this->fsmObjects(), $statusKey);
        if (!$fsmObject) {
            throw new \RuntimeException('FSM object key ' . $statusKey . ' does not exists.');
        }

        return $fsmObject;
    }

    /**
     * 获取 FSM 对象列表
     *
     * @return \Package\FSM\FSM[]
     */
    protected function fsmObjects(): array
    {
        if ($this->fsmObjects) {
            return $this->fsmObjects;
        }

        $this->fsmTransitions = $this->fsmTransitions();
        $this->fsmConstants   = $this->fsmConstants();

        foreach ($this->fsmConstants as $statusKey => $constantKey) {
            $transitions = Arr::get($this->fsmTransitions, $statusKey);
            if (!$transitions) {
                throw new \RuntimeException(static::class . ' has no transitions for status key ' . $statusKey . '.');
            }

            $currentStateKey = cons()->key($constantKey, $this->{$statusKey});
            $payload         = [$this, $statusKey];
            $fsmObject       = new FSM($currentStateKey, $payload);
            // important: unlink reference array
            unset($payload);

            foreach ($transitions as $symbol => $states) {
                foreach ($states as $state => $nextState) {
                    // 检查key是否正常，防止低级错误
                    if (!is_string($state)) {
                        throw new InternalServerException('fsmTransitions() states must be string.');
                    }

                    $fsmObject->addTransition($symbol, $state, $nextState, [static::class, 'fsmChangeState']);
                }
            }

            $this->fsmObjects[$statusKey] = $fsmObject;
        }
        return $this->fsmObjects;
    }

    /**
     * FSM 事务转换列表
     *
     * @return array
     */
    abstract protected function fsmTransitions(): array;

    /**
     * FSM 常量列表
     *
     * @return array
     */
    abstract protected function fsmConstants(): array;

    /**
     * FSM 先前状态
     *
     * @param string $statusKey
     * @param string $symbol
     *
     * @return bool
     */
    public function fsmPrevIs(string $statusKey, string $symbol): bool
    {
        return $this->fsmObject($statusKey)->getPreviousState() === $symbol;
    }

    /**
     * FSM 判断当前是否可以执行
     *
     * @param string $statusKey
     * @param string $symbol
     *
     * @return bool
     */
    public function fsmCan(string $statusKey, string $symbol): bool
    {
        return $this->fsmObject($statusKey)->can($symbol);
    }

    /**
     * FSM 执行事务
     *
     * @param string     $statusKey
     * @param string     $symbol
     * @param array|null $extends
     *
     * @return bool
     */
    public function fsmProcess(string $statusKey, string $symbol, ?array $extends = null): bool
    {
        if (!$this->fsmObject($statusKey)->process($symbol, $extends)) {
            $currentStatusKey = cons()->key($this->fsmConstants[$statusKey], $this->{$statusKey});
            throw new StatusLockException(__CLASS__ . " 状态切换异常: 操作指令 = {$symbol}, ID = {$this->id}, currentState = {$currentStatusKey}");
        }

        return true;
    }

    /**
     * 根据状态value获取key
     *
     * @param string $statusKey
     * @param int    $state
     *
     * @return string
     */
    public function fsmStateKeyForValue(string $statusKey, int $state): string
    {
        return cons()->key($this->fsmConstants[$statusKey], $state);
    }
}

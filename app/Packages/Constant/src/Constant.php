<?php

namespace Package\Constant;

class Constant
{
    private const KEY = '_key';
    private const VALUE = '_value';

    /**
     * All the constant items by key.
     *
     * @var array
     */
    protected $keyItems = [];

    /**
     * Index for search key by value.
     *
     * @var array
     */
    protected $valueIndexItems = [];

    /**
     * The path of constant files.
     *
     * @var string
     */
    protected $path;

    /**
     * The Filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $compiler;

    /**
     * Constant locale.
     *
     * @var string
     */
    protected $locale;

    /**
     * Constant fallback locale.
     *
     * @var string
     */
    protected $fallbackLocale;

    /**
     * Create a new constant instance.
     *
     * @param \Package\Constant\ConstantCompiler $compiler
     * @param string                            $path
     */
    public function __construct(ConstantCompiler $compiler, $path)
    {
        $this->compiler       = $compiler;
        $this->path           = $path;
        $this->locale         = $compiler->getLocale();
        $this->fallbackLocale = $compiler->getFallbackLocale();

        ['key' => $this->keyItems, 'value' => $this->valueIndexItems] = $this->compiledConstants($path);
    }

    /**
     * Determine if the given constant key exists.
     *
     * @param  string $key
     *
     * @return bool
     */
    public function has($key)
    {
        return array_key_exists($key, $this->keyItems);
    }

    /**
     * Determine if the given constant value exists.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return bool
     */
    public function hasValue($key, $value)
    {
        return array_key_exists("$key.$value", $this->valueIndexItems);
    }

    /**
     * Get the constant for the given key.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function get($key)
    {
        if (!$this->has($key)) {
            throw new \InvalidArgumentException('Constant key \'' . $key . '\' does not exists.');
        }

        $item = $this->keyItems[$key];
        // 如果是末端，则返回对应的value
        if ($this->isItem($item)) {
            return $item[static::VALUE];
        }

        // 否则返回数组
        return $item;
    }

    /**
     * Get constant key by value.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return array|string
     */
    public function key($key, $value = null)
    {
        if (!$this->has($key)) {
            throw new \InvalidArgumentException('Constant key \'' . $key . '\' does not exists.');
        }

        $keyValues = $this->keyItems[$key];
        if ($this->isItem($keyValues)) {
            throw new \InvalidArgumentException('Constant key \'' . $key . '\' is invalid.');
        }

        $valueKeys = array_flip($keyValues);
        // 没有传value，直接返回所有字段
        if (func_num_args() === 1) {
            return $valueKeys;
        }

        return $valueKeys[$value];
    }

    /**
     * Get the lang for the given key.
     *
     * @param string      $key
     * @param null|string $locale
     *
     * @return array|string
     */
    public function lang($key, $locale = null)
    {
        if (!$this->has($key)) {
            throw new \InvalidArgumentException('Constant key \'' . $key . '\' does not exists.');
        }

        $item = $this->keyItems[$key];
        // 末端直接返回单个翻译
        if ($this->isItem($item)) {
            return $this->getItemLang($item, $locale);
        }

        // 否则循环翻译后返回
        $keyLangs = [];
        foreach ($item as $subKey => $value) {
            $keyLangs[$subKey] = $this->getItemLang($this->keyItems["{$key}.{$subKey}"]);
        }

        return $keyLangs;
    }

    /**
     * Get lang from constant through value.
     *
     * @param string      $key
     * @param mixed       $value
     * @param null|string $locale
     *
     * @return array|string
     */
    public function valueLang($key, $value = null, $locale = null)
    {
        if (!$this->has($key)) {
            throw new \InvalidArgumentException('Constant key \'' . $key . '\' does not exists.');
        }

        $keyValues = $this->keyItems[$key];
        if ($this->isItem($keyValues)) {
            throw new \InvalidArgumentException('Constant key \'' . $key . '\' is invalid.');
        }

        // 没有传value，将所有字段翻译后返回
        if (func_num_args() === 1) {
            $valueLangs = [];
            foreach ($keyValues as $subKey => $value) {
                $valueLangs[$value] = $this->getItemLang($this->keyItems["{$key}.{$subKey}"]);
            }
            return $valueLangs;
        }

        $itemKey = array_search($value, $keyValues);
        // 如果找不到对应value，直接返回null
        if ($itemKey === false) {
            return null;
        }

        // 如果找到，显示对应描述
        $item = $this->keyItems["{$key}.{$itemKey}"];
        return $this->getItemLang($item, $locale);
    }

    /**
     * Get value from constant through lang.
     *
     * @param string      $key
     * @param mixed|null  $value
     * @param string|null $locale
     *
     * @return string
     */
    public function langValue(string $key, mixed $value = null, string $locale = null)
    {
        $arr = array_flip($this->valueLang($key));
        $val = null;
        if (array_key_exists($key, $arr)){
            $val = $arr[$key];
            unset($arr[$key]);
        }
        return $val;
    }

    /**
     * 判断是否常量末端
     *
     * @param mixed $item
     *
     * @return bool
     */
    protected function isItem($item)
    {
        return is_array($item) && isset($item[static::KEY]);
    }

    /**
     * 根据Key获取Item的Lang
     *
     * @param array       $item
     * @param null|string $locale
     *
     * @return string
     */
    protected function getItemLang($item, $locale = null)
    {
        $locale = $locale ?: $this->locale;
        if (isset($item[$locale])) {
            return $item[$locale];
        }

        $fallbackLocale = $this->fallbackLocale;
        if (isset($item[$fallbackLocale])) {
            return $item[$fallbackLocale];
        }

        return $item[static::KEY];
    }

    /**
     * Compile the constants.
     *
     * @param string $path
     *
     * @return array
     */
    protected function compiledConstants($path)
    {
        if ($this->compiler->isExpired($path)) {
            $this->compiler->compile($path);
        }

        return require $this->compiler->getCompiledPath();
    }
}

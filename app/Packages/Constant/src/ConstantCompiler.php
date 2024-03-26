<?php

namespace Package\Constant;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class ConstantCompiler
{

    /**
     * The Filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * The file currently being compiled.
     *
     * @var string
     */
    protected $path;

    /**
     * Get the cache path for the compiled views.
     *
     * @var string
     */
    protected $compiledPath;

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
     * Create a new compiler instance.
     *
     * @param \Illuminate\Filesystem\Filesystem $files
     * @param string                            $compiledPath
     * @param string                            $locale
     * @param string                            $fallbackLocale
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(Filesystem $files, $compiledPath, $locale = 'en', $fallbackLocale = 'en')
    {
        if (!$compiledPath) {
            throw new \InvalidArgumentException('Please provide a valid compiled path.');
        }

        $this->files          = $files;
        $this->compiledPath   = $compiledPath;
        $this->locale         = $locale;
        $this->fallbackLocale = $fallbackLocale;
    }

    /**
     * Get the path currently being compiled.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set the path currently being compiled.
     *
     * @param string $path
     *
     * @return void
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * Get the path to the compiled version of constant.
     *
     * @param string $path
     *
     * @return string
     */
    public function getCompiledPath()
    {
        return $this->compiledPath;
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @return string
     */
    public function getFallbackLocale()
    {
        return $this->fallbackLocale;
    }

    /**
     * Determine if the view at the given path is expired.
     *
     * @param string $path
     *
     * @return bool
     */
    public function isExpired($path)
    {
        if (!$this->files->exists($this->compiledPath)) {
            return true;
        }

        // 判断每个常量文件修改时间，是否超过编译后的时间
        $compiledLastModified = $this->files->lastModified($this->compiledPath);
        foreach ($this->constantFilesInPath($path) as $file) {
            if ($compiledLastModified < $file->getMTime()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Compile the constant at the given path.
     *
     * @param string $path
     *
     * @return void
     */
    public function compile($path = null)
    {
        if (!is_null($path)) {
            $this->setPath($path);
        }

        if (!is_null($this->compiledPath)) {
            $this->compileConstants($path);
        }
    }

    /**
     * Compile constants to cache file.
     *
     * @param string $path
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    protected function compileConstants($path)
    {
        $sources = $this->loadConstants($path);
        [$constants['key'], $constants['value']] = $this->parseConstants($sources);

        $written = $this->files->put(
            $this->compiledPath, '<?php return ' . var_export($constants, true) . ';' . PHP_EOL
        );
        $this->files->chmod($this->compiledPath, 0777);

        if ($written === false) {
            throw new \RuntimeException('The constant cache file cound not be created at ' . $this->compiledPath . '.');
        }
    }

    /**
     * Format constant sources.
     *
     * @param string $path
     *
     * @return array
     */
    protected function parseConstants($sources, $parentKey = null)
    {
        $keyConstants    = [];
        $valueConstants  = [];
        $parentConstants = [];
        $hasChildren     = false;

        foreach ($sources as $subKey => $subSources) {
            $key = $parentKey ? "{$parentKey}.$subKey" : $subKey;

            if (is_array($subSources) && !isset($subSources[0])) {
                $hasChildren = true;
                // 非最终节点，解析下一层
                [$subKeyConstants, $subValueConstants] = $this->parseConstants($subSources, $key);

                $keyConstants   = array_merge($keyConstants, $subKeyConstants);
                $valueConstants = array_merge($valueConstants, $subValueConstants);
            } else {
                // 最终节点，开始转换成成通用结构
                $subConstants = (array)$subSources;
                $value        = Arr::pull($subConstants, 0);
                $lang         = Arr::pull($subConstants, 1);
                $fallbackLang = Arr::pull($subConstants, 2);

                if (is_null($value)) {
                    throw new \InvalidArgumentException('Constant value must not be null.');
                }

                if ($lang && !array_key_exists($this->locale, $subConstants)) {
                    $subConstants[$this->locale] = $lang;
                }

                if ($fallbackLang && !array_key_exists($this->fallbackLocale, $subConstants)) {
                    $subConstants[$this->fallbackLocale] = $fallbackLang;
                }

                $subConstants['_key']   = $subKey;
                $subConstants['_value'] = $value;

                $valueKey                  = $parentKey ? "{$parentKey}.{$value}" : $value;
                $keyConstants[$key]        = $subConstants;
                $valueConstants[$valueKey] = $subKey;

                // 提取key-value作为单独一个字段
                $keyValues                   = Arr::get($parentConstants, $parentKey, []);
                $keyValues[$subKey]          = $value;
                $parentConstants[$parentKey] = $keyValues;
            }
        }

        if (!$hasChildren) {
            $keyConstants = array_merge($keyConstants, $parentConstants);
        }

        return [$keyConstants, $valueConstants];
    }

    /**
     * Get the constant file nesting path.
     *
     * @param \Symfony\Component\Finder\SplFileInfo $file
     * @param string                                $path
     *
     * @return string
     */
    protected function getNestedDirectory(SplFileInfo $file, $path)
    {
        $directory = $file->getPath();
        if ($nested = trim(str_replace($path, '', $directory), DIRECTORY_SEPARATOR)) {
            $nested = str_replace(DIRECTORY_SEPARATOR, '.', $nested) . 'src';
        }

        return $nested;
    }

    /**
     * Grab all constant files.
     *
     * @param string $path
     *
     * @return \Symfony\Component\Finder\Finder
     * @throws \InvalidArgumentException
     */
    protected function constantFilesInPath($path)
    {
        return Finder::create()->files()->name('*.php')->in($path);
    }

    /**
     * Get all of the constant files for the application.
     *
     * @param string $path
     *
     * @return array
     * @throws \InvalidArgumentException
     */
    public function loadConstants($path)
    {
        $constants = [];
        foreach ($this->constantFilesInPath($path) as $file) {
            $directory   = $this->getNestedDirectory($file, $path);
            $constantKey = $directory . basename($file->getRealPath(), '.php');

            if (Arr::has($constants, $constantKey)) {
                throw new \InvalidArgumentException("Constant key '{$constantKey}' already exists. Conflict file '{$file->getRealPath()}'.");
            }

            Arr::set($constants, $constantKey, require $file->getRealPath());
        }

        return $constants;
    }
}

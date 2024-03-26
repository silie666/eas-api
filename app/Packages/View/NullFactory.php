<?php

namespace Package\View;

use Illuminate\Contracts\View\Factory;

class NullFactory implements Factory
{
    public function creator($views, $callback)
    {
    }

    public function addNamespace($namespace, $hints)
    {
    }

    public function file($path, $data = [], $mergeData = [])
    {
    }

    public function replaceNamespace($namespace, $hints)
    {
    }

    public function composer($views, $callback)
    {
    }

    public function exists($view)
    {
    }

    public function share($key, $value = null)
    {
    }

    public function make($view, $data = [], $mergeData = [])
    {
    }
}
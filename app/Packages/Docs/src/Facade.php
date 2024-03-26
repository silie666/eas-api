<?php

namespace Package\ApiDocs;

use Illuminate\Support\Facades\Facade as BaseFacade;

class Facade extends BaseFacade
{
    protected static function getFacadeAccessor(): string
    {
        return 'api-docs';
    }
}

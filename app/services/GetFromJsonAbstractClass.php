<?php

namespace App\Services;

use Exception;

abstract class GetFromJsonAbstractClass
{

    public function value(string $string)
    {
        return 0;
    }
    public function all()
    {
        return [];
    }
    public function count(string $key = "")
    {
        return 0;
    }
}

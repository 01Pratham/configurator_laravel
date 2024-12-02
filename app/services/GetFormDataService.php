<?php

namespace App\Services;

use Exception;

class GetFormDataService
{
    protected array $array;

    public function __construct(string $json_string = "{}")
    {
        $this->array = json_decode($json_string, true);
    }
    public function value(string $string, $outputType = "string", array $arr = null)
    {
        if (empty($this->array)) {
            return "";
        }
        try {
            if ($arr === null) $arr = $this->array;
            if ($string === "") return $arr;
            $keyArr = explode(".", $string);
            $key = $keyArr[0];
            array_shift($keyArr);
            if (gettype($arr[$key]) == "array" && gettype($outputType) == "string") {
                return $this->value(implode(".", $keyArr), "string", $arr[$key]);
            }
            return $arr[$key];
        } catch (Exception $e) {
            return "";
        }
    }
}

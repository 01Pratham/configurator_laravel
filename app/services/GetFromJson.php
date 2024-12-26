<?php

namespace App\Services;

use Exception;
use Symfony\Component\CssSelector\Node\FunctionNode;

class GetFromJson extends GetFromJsonAbstractClass
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
    public function all()
    {
        return $this->array;
    }

    public function count(string $key = "", $val = ""): int
    {
        if ($key == "") return count($this->array);
        if ($val == "") return count($this->array[$key]);
        return $this->FindOccurence(key: $key, val: $val, arr: $this->array);
    }
    private function FindOccurence(string $key, string $val, array $arr = null): int
    {
        if ($arr === null) {
            $arr = $this->array; // Handle the default value here
        }

        $count = 0;
        foreach ($arr as $k => $v) {
            if ($k == $key && $v == $val) {
                $count++;
            }
            if (is_array($v)) {
                $count += $this->FindOccurence(key: $key, val: $val, arr: $v);
            }
        }
        return $count;
    }
}

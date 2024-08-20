<?php

if (!function_exists('INR')) {
    function INR($number)
    {
        $integerPart = floor($number);
        $decimalPart = $number - $integerPart;

        $money = strrev($integerPart);
        $length = strlen($money);
        $delimiter = '';

        for ($i = 0; $i < $length; $i++) {
            if (($i == 3 || ($i > 3 && ($i - 1) % 2 == 0)) && $i != $length) {
                $delimiter .= ',';
            }
            $delimiter .= $money[$i];
        }

        $result = strrev($delimiter);

        if ($decimalPart > 0) {
            $decimalPart = number_format($decimalPart, 2, '.', '');
            $result .= substr($decimalPart, 1); // Append the decimal part
        }

        return "&#8377; " . $result;
    }
}
if (!function_exists('PRE')) {
    function PRE(mixed $ARRAY, bool $exit = false): void
    {
        echo "<pre>";
        print_r($ARRAY);
        echo "</pre>";
        ($exit) ? exit : null;
    }
}

if (!function_exists("modify")) {
    function modify(string $string): string
    {
        $modifiedString = preg_replace('/([A-Z])/', ' $1', $string);
        $modifiedString = trim($modifiedString);
        return ucwords($modifiedString);
    }
}


if (!function_exists('get_sql')) {
    function get_sql($query)
    {
        // Get the raw SQL query
        $sql = $query->toSql();

        // Get the bindings
        $bindings = $query->getBindings();

        // Interpolate the bindings into the SQL query
        $fullSql = vsprintf(str_replace('?', '%s', $sql), collect($bindings)->map(function ($binding) {
            return is_numeric($binding) ? $binding : "'{$binding}'";
        })->toArray());

        return $fullSql;
    }
}

if (!function_exists("arranage_keys")) {
    function arrange_keys($arranged_array, $array): array
    {
        $rearrangedArray = [];
        foreach ($arranged_array as $key => $value) {
            if (array_key_exists($key, $array)) {
                $rearrangedArray[$key] = $array[$key];
            }
        }
        foreach ($array as $key => $value) {
            if (!array_key_exists($key, $rearrangedArray)) {
                $rearrangedArray[$key] = $value;
            }
        }
        return $rearrangedArray;
    }
}


if (!function_exists("API")) {
    function API($url)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Basic Y3JtaWFwaWNsaWVudDo2QUc/eFIkczQ7UDkkPz8hSw=='
            ),
        ));

        $response = curl_exec($curl);

        if (curl_errno($curl)) {
            echo 'cURL Error: ' . curl_error($curl);
        }
        curl_close($curl);

        return json_decode($response, true);
    }
}

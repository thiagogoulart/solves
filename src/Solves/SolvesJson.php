<?php
/**
 * @author Thiago G.S. Goulart
 * @version 1.0
 * @created 18/07/2019
 */ 
namespace Solves;


class SolvesJson {
    
    public static function arrayFromDaoToString($array, $sep) {
        $str = '';
        $first = true;
        foreach ($array as $i) {
            if (!$first) {
                $str .= $sep;
            }
            $str .= $i[0];
            $first = false;
        }
        return $str;
    }

    public static function arrayFromDaoToArray($array) {
        $arr = array();
        foreach ($array as $i) {
            $arr[] = $i[0];
        }
        return $arr;
    }

    public static function arrayFromDaoToJson($array, $pkName) {
        $sep = ',';
        $str = '[{';
        $first = true;
        foreach ($array as $i) {
            if (!$first) {
                $str .= $sep;
            }
            $j = SolvesJson::array_to_json($i, $pkName, true);
            $str .= '' . $j . '';
            $first = false;
        }
        $str .= '}]';
        return $str;
    }
    public static function objectToArrayFromDaoToJson($obj, $pkName) {
        $str = '[{';
        $j = array_to_json($obj, $pkName, true);
        $str .= '' . $j . '';
        $str .= '}]';
        return $str;
    }

    public static function arrayFromDaoToJsonExcept($array, $pkName, $arrExcept) {
        $sep = ',';
        $str = '[{';
        $first = true;
        foreach ($array as $i) {
            if (!$first) {
                $str .= $sep;
            }
            $j = SolvesJson::array_to_json_except($i, $pkName, true, $arrExcept);
            $str .= '' . $j . '';
            $first = false;
        }
        $str .= '}]';
        return $str;
    }
    public static function array_to_json($array, $pkName, bool $duplaContraBarra=true) {
        if (!is_array($array)) {
            return false;
        }
        $associative = count(array_diff(array_keys($array), array_keys(array_keys($array))));
        if ($associative) {
            $construct = array();
            $result = '{';
            $first = true;
            $pk = '';
            foreach ($array as $key => $value) {
                if (is_numeric($key)) {
                    continue;
                }
                if ($key == $pkName) {
                    $pk = $value;
                }
                $key = SolvesJson::escape_query_com_aspas($key, @$duplaContraBarra);

                // Format the value:
                if (is_array($value)) {
                    $value = SolvesJson::array_to_json($value);
                } else if (!is_numeric($value) || is_string($value)) {
                    $value = SolvesJson::escape_query_com_aspas($value, @$duplaContraBarra);
                }

                if (!$first) {
                    $result .= ',';
                }
                // Add to staging array:
                $result .= '' . $key . ':' . $value;
                $first = false;
            }
            $result .= '}';
            $pk = SolvesJson::escape_query_com_aspas($pk, @$duplaContraBarra);
            $result = $pk . ':' . $result;
        }
        return $result;
    }


    public static function getJsonByArrayItemFromDao(array $array=null, string $pkName=null, bool $duplaContraBarra){
         if (!is_array($array)) {
            return false;
        }
        $associative = count(array_diff(array_keys($array), array_keys(array_keys($array))));
        if ($associative) {
            $construct = array();
            $result = '{';
            $first = true;
            $pk = '';
            foreach ($array as $key => $value) {
                if (is_numeric($key)) {
                    continue;
                }
                if ($key == $pkName) {
                    $pk = $value;
                }
                $key = SolvesJson::escape_query_com_aspas($key, @$duplaContraBarra);

                // Format the value:
                if (is_array($value)) {
                    $value = SolvesJson::array_to_json($value);
                } else if (!is_numeric($value) || is_string($value)) {
                    $value = SolvesJson::escape_query_com_aspas($value, @$duplaContraBarra);
                }

                if (!$first) {
                    $result .= ',';
                }
                // Add to staging array:
                $result .= '' . $key . ':' . $value;
                $first = false;
            }
            $result .= '}';
            $pk = SolvesJson::escape_query_com_aspas($pk, @$duplaContraBarra);
        }
        return $result;
    }
    public static function array_to_json_except($array, $pkName, $duplaContraBarra, $arrExcept) {
        if (!is_array($array)) {
            return false;
        }
        $associative = count(array_diff(array_keys($array), array_keys(array_keys($array))));
        if ($associative) {
            $construct = array();
            $result = '{';
            $first = true;
            $pk = '';
            foreach ($array as $key => $value) {
                if (is_numeric($key) || in_array($key, $arrExcept)) {
                    continue;
                }
                if ($key == $pkName) {
                    $pk = $value;
                }
                $key = SolvesJson::escape_query_com_aspas($key, @$duplaContraBarra);

                // Format the value:
                if (is_array($value)) {
                    $value = SolvesJson::array_to_json($value);
                } else if (!is_numeric($value) || is_string($value)) {
                    $value = SolvesJson::escape_query_com_aspas($value, @$duplaContraBarra);
                }

                if (!$first) {
                    $result .= ',';
                }
                // Add to staging array:
                $result .= '' . $key . ':' . $value;
                $first = false;
            }
            $result .= '}';
            $pk = SolvesJson::escape_query_com_aspas($pk, @$duplaContraBarra);
            $result = $pk . ':' . $result;
        }
        return $result;
    }
    public static function json_to_array($json) {
        return json_decode($json);
    }
    public static function getJsonFieldValue($v){
        return urldecode($v);
    }
    public static function escape_query(string $str=null, bool $duplaContraBarra) {
        return strtr($str, array(
            "\0" => "",
            "'" => "&#39;",
            '"' => (isset($duplaContraBarra) ? ($duplaContraBarra == true ? "&#92;" : "") : "&#92;") . "&#34;",
            "\\" => "&#92;",
            // more secure
            "<" => "&lt;",
            ">" => "&gt;",
            "\r" => " ",
            "\n" => " ",
            "\t" => " "
        ));
    }
    public static function escape_query_com_aspas(string $str=null, bool $duplaContraBarra) {
        return '"' . SolvesJson::escape_query($str, @$duplaContraBarra) . '"';
    }

}
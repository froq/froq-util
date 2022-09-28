<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
declare(strict_types=1);

// Dirty debug/dump tools.. :(
if (!function_exists('_ps')) {
    function _ps($s) {
        if (is_null($s)) return 'NULL';
        if (is_bool($s)) return $s ? 'TRUE' : 'FALSE';
        return preg_replace('~\[(.+?):.+?:(private|protected)\]~', '[\1:\2]', print_r($s, true));
    }
}
if (!function_exists('_pd')) {
    function _pd($s) {
        ob_start(); var_dump($s); $s = ob_get_clean();
        return preg_replace('~\["?(.+?)"?(:(private|protected))?\]=>\s+~', '[\1\2] => ', _ps(trim($s)));
    }
}
if (!function_exists('pre')) {
    function pre($s, $e=0) {
        echo "<pre>", _ps($s), "</pre>", "\n";
        $e && exit;
    }
}
if (!function_exists('prs')) {
    function prs($s, $e=0) {
        echo _ps($s), "\n";
        $e && exit;
    }
}
if (!function_exists('prd')) {
    function prd($s, $e=0) {
        echo _pd($s), "\n";
        $e && exit;
    }
}

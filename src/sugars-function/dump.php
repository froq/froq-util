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
if (!function_exists('prc')) {
    function prc($s, $t='i', $e=0) {
        match ($t) {
            'e' => prs("\033[31m{$s}\033[0m", $e), // error
            's' => prs("\033[32m{$s}\033[0m", $e), // success
            'w' => prs("\033[33m{$s}\033[0m", $e), // warning
            'i' => prs("\033[36m{$s}\033[0m", $e), // info
            default => prs($s, 1)
        };
    };
}

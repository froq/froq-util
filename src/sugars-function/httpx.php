<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
declare(strict_types=1);

/**
 * On-demand function used by http_parse_header().
 *
 * @param  string     $header
 * @param  string|int $case
 * @param  bool       $verbose
 * @return array
 * @since  6.0
 * @internal
 */
function httpx_parse_header(string $header, string|int $case = null, bool $verbose = false): array
{
    $data = [];

    [$name, $value] = split(' *: *', $header, 2);
    if (!isset($name)) {
        return $data;
    }

    $name  = trim($name);
    $value = trim($value ?? '');

    // Apply case conversion.
    if ($case !== null) {
        $data = convert_case($data, $case, '-');
    }

    // Normalize value.
    $value = preg_replace_callback('~\s*([;,\s]+)~', function ($m) {
        $char = $m[1][0];
        return ($char == ';' || $char == ',') ? $char . ' ' : $char;
    }, $value);

    $data = ['name' => $name, 'value' => $value];

    if (!$verbose) {
        return $data;
    }

    $detail = [];

    // List: https://en.wikipedia.org/wiki/List_of_HTTP_header_fields
    switch ($name = strtoupper($name)) {
        case 'ACCEPT':
        case 'ACCEPT-CHARSET':
        case 'ACCEPT-ENCODING':
        case 'ACCEPT-LANGUAGE':
            if (preg_match_all('~([^;]+)(?:; *q=([^,]+))?~i', $value, $match, PREG_UNMATCHED_AS_NULL)) {
                $type_list = $quality_list = [];
                foreach ($match[1] as $i => $types) {
                    foreach (split(' *, *', $types) as $type) {
                        $quality = isset($match[2][$i]) ? floatval($match[2][$i]) : null;
                        $type_list[] = $type;
                        if ($quality !== null) {
                            $quality_list[] = $quality;
                        }

                        $detail[] = ['type' => $type, 'quality' => $quality];
                    }
                }

                $detail['type_list'] = $type_list;
                $detail['quality_list'] = $quality_list;
            }
            break;

        case 'AUTHORIZATION':
        case 'PROXY-AUTHORIZATION':
        case 'PROXY-AUTHENTICATE':
        case 'WWW-AUTHENTICATE':
            if (preg_match('~([\w\-]+) +(.+)~', $value, $match)) {
                [, $scheme, $params] = $match;
                $detail = ['scheme' => $scheme, 'params' => '', 'parsed_params' => []];
                if ($params != '') {
                    // Wrap all non-quoted stuff in quotes (not base64 stuff).
                    if (str_contains($params, ' ') || preg_test('~(\w+)=([^=]+)~', $params)) {
                        $params = preg_replace('~(\w+)=([^"]+?)(,|$)~', '\1="\2"\3', trim($params));
                    }

                    if (!str_contains($params, ' ') && strtoupper($scheme) == 'BASIC') {
                        $temp = split(':', (string) base64_decode($params, true), 2);
                        if (array_filter($temp)) {
                            $parsed_params = array_compose(['user', 'pass'], $temp);
                        }
                    } elseif (preg_match_all('~(\w+)="([^"]+)~', $params, $match)) {
                        $parsed_params = array_compose($match[1], $match[2]);
                    }

                    $detail['params']        = $params;
                    $detail['parsed_params'] = $parsed_params ?? ['token' => $params];
                }
            }
            break;

        case 'CACHE-CONTROL':
            foreach (split(' *, *', $value) as $control) {
                [$key, $val] = split('=', $control, 2);
                if ($key !== null) {
                    $key = strtolower($key);
                    $detail[$key] = ($val !== null) ? intval($val) : true;
                }
            }
            break;

        case 'CONTENT-TYPE':
        case 'CONTENT-DISPOSITION':
            $pattern = ($name == 'CONTENT-TYPE') ? '~([^/]+/[^;]+)(?:; *(.+))?~' : '~([^;]+)(?:; *(.+))?~';
            if (preg_match($pattern, $value, $match)) {
                [, $type, $params] = array_pad($match, 3, '');
                $detail = ['type' => $type, 'params' => '', 'parsed_params' => []];
                if ($params != '') {
                    // Wrap all non-quoted stuff in quotes.
                    if (str_contains($params, ' ') || preg_test('~(\w+)=([^"]+)~', $params)) {
                        $params = preg_replace('~(\w+)=([^"]+?)(\s|$)~', '\1="\2"\3', trim($params));
                    }

                    if (preg_match_all('~(\w+)="([^"]+)~', $params, $match)) {
                        $parsed_params = array_compose($match[1], $match[2]);
                    }

                    $detail['params']        = $params;
                    $detail['parsed_params'] = $parsed_params ?? [];
                }
            }
            break;

        case 'CONTENT-RANGE':
            if (preg_match('~(\w+) +(?:(\*)/(\d+)|(\d+)-(\d+)/(\d+|\*))~', $value, $match)) {
                $detail = ['unit' => $match[1]];
                if (count($match) == 4) {
                    $detail['size']  = (int) $match[3];
                    $detail['range'] = $match[2];
                } else {
                    $detail['size']  = ($match[6] != '*') ? (int) $match[6] : '*';
                    $detail['range'] = [(int) $match[4], (int) $match[5]];
                }
            }
            break;

        case 'CONTENT-LENGTH':
            $detail = ['length' => intval($value)];
            break;

        case 'COOKIE':
            foreach (split(' *; *', $value) as $cookie) {
                [$key, $val] = split('=', $cookie, 2);
                if ($key !== null) {
                    $detail[$key] = $val;
                }
            }
            break;

        case 'DIGEST':
            if (preg_match_all('~([\w\-]+)=([^, ]+)~', $value, $match)) {
                $detail = array_compose($match[1], $match[2]);
            }
            break;

        case 'EXPECT-CT':
            if (preg_match_all('~max-age=\d+|report-uri=".+?"|enforce~i', $value, $match)) {
                foreach ($match[0] as $param) {
                    $para = strtolower($param);
                    if (str_starts_with($para, 'max-age')) {
                        $detail['max_age'] = intval(split('=', $para)[1]);
                    } elseif (str_starts_with($para, 'report-uri')) {
                        $detail['report_uri'] = trim(split('=', $param)[1], '"');
                    } else {
                        $detail['enforce'] = true;
                    }
                }
            }
            break;

        case 'FORWARDED':
            foreach (split(' *; *', $value) as $fields) {
                foreach (split(' *, *', $fields) as $field) {
                    [$key, $val] = split('=', $field, 2);
                    if ($key !== null) {
                        $detail[$key] = isset($detail[$key])
                            ? [...(array) $detail[$key], $val] : $val;
                    }
                }
            }
            break;

        case 'HOST':
        case 'ORIGIN':
        case 'REFERER':
        case 'LOCATION':
        case 'CONTENT-LOCATION':
            $detail = ['url' => $value];
            break;

        case 'IF-RANGE':
        case 'IF-MATCH':
        case 'IF-NONE-MATCH':
            if ($name == 'IF-RANGE' && str_ends_with($value, 'GMT')) {
                $detail = ['date' => $value, 'time' => strtotime($value)];
            } else {
                $detail = ['etags' => split(' *, *', $value)];
            }
            break;

        case 'LINK':
            foreach (split(' *, *<', $value) as $i => $part) {
                $pos = strpos($part, '>;');
                if ($pos === false) {
                    continue;
                }

                $part = ltrim($part, '<');
                $link = substr($part, 0, $pos);
                $rest = substr($part, strlen($link) + 2);
                foreach (split(' *; *', $rest) as $param) {
                    [$key, $val] = split('=', $param, 2);
                    if ($key !== null) {
                        $params[$key] = trim($val, '"');
                    }
                }

                $detail[$i]['link']   = $link;
                $detail[$i]['params'] = $params ?? [];
            }
            break;

        case 'KEEP-ALIVE':
            $detail = array_reduce(split(' *, *', $value), function ($acc, $param) {
                [$key, $val] = split('=', $param, 2);
                return [...$acc, ...[$key => (int) $val]];
            }, []);
            break;

        case 'RANGE':
            if (preg_match('~(\w+)=(.+)~', $value, $match)) {
                $detail = ['unit' => $match[1]];
                foreach (split(' *, *', $match[2]) as $i => $range) {
                    if (preg_match('~(\d*)(-)(\d*)~', $range, $match)) {
                        $range = array_slice($match, 1);
                        $detail['range'][$i] = join($range);
                        $detail['range_list'][$i] = [
                            ($range[0] != '') ? (int) $range[0] : null,
                            ($range[2] != '') ? (int) $range[2] : null,
                        ];
                    }
                }
            }
            break;

        case 'SET-COOKIE':
            $detail = http_parse_cookie($value);
            break;

        case 'STRICT-TRANSPORT-SECURITY':
            if (preg_match_all('~max-age=\d+|includeSubDomains|preload~i', $value, $match)) {
                foreach ($match[0] as $param) {
                    $para = strtolower($param);
                    if (str_starts_with($para, 'max-age')) {
                        $detail['max_age'] = intval(split('=', $para)[1]);
                    } elseif (str_starts_with($para, 'include')) {
                        $detail['include_sub_domains'] = true;
                    } else {
                        $detail['preload'] = true;
                    }
                }
            }
            break;

        case 'STATUS':
            if (preg_match('~(\d+)(?: +(.+))?~', $value, $match)) {
                $detail['code'] = intval($match[1]);
                $detail['text'] = trim($match[2] ?? '') ?: null;
            }
            break;

        // Date-time stuff.
        case 'DATE':
        case 'LAST-MODIFIED':
        case 'IF-MODIFIED-SINCE':
        case 'IF-UNMODIFIED-SINCE':
            $detail = ['date' => $value, 'time' => strtotime($value)];
            break;

        // Comma-separated stuff.
        case 'ACCEPT-CH':
        case 'ACCESS-CONTROL-REQUEST-METHOD':
        case 'ACCESS-CONTROL-REQUEST-HEADERS':
        case 'ACCESS-CONTROL-ALLOW-CREDENTIALS':
        case 'ACCESS-CONTROL-ALLOW-HEADERS':
        case 'ACCESS-CONTROL-ALLOW-METHODS':
        case 'ACCESS-CONTROL-ALLOW-ORIGIN':
        case 'ACCESS-CONTROL-EXPOSE-HEADERS':
        case 'ACCEPT-POST':
        case 'ACCEPT-PATCH':
        case 'ALLOW':
        case 'CONNECTION':
        case 'PROXY-CONNECTION':
        case 'CONTENT-ENCODING':
        case 'CONTENT-LANGUAGE':
        case 'UPGRADE':
        case 'VARY':
        case 'VIA':
            $detail = split(' *, *', $value);
            break;
    }

    // Finally..
    $data['detail'] = $detail;

    return $data;
}

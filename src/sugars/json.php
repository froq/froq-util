<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */

/**
 * Short builders.
 */
function json($data, ...$args) {
    return Json::build($data, ...$args);
}
function json_pretty($data, ...$args) {
    return Json::buildPretty($data, ...$args);
}
function json_null($data, ...$args) {
    return $data ? Json::build($data, ...$args) : null;
}

/**
 * Builders.
 */
function json_build($data, ...$args) {
    return Json::build($data, ...$args);
}
function json_build_pretty($data, ...$args) {
    return Json::buildPretty($data, ...$args);
}
function json_build_array($data, ...$args) {
    return Json::buildArray($data, ...$args);
}
function json_build_object($data, ...$args) {
    return Json::buildObject($data, ...$args);
}

/**
 * Parsers.
 */
function json_parse($json, ...$args) {
    return Json::parse($json, ...$args);
}
function json_parse_array($json, ...$args) {
    return Json::parseArray($json, ...$args);
}
function json_parse_object($json, ...$args) {
    return Json::parseObject($json, ...$args);
}

/**
 * Checkers.
 */
function is_json_array($input) {
    return Json::isArray($input);
}
function is_json_object($input) {
    return Json::isObject($input);
}
function is_json_struct($input) {
    return Json::isValidStruct($input);
}

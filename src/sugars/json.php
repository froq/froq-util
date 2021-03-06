<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
declare(strict_types=1);

use froq\util\misc\Json;

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
function is_json_array($in) {
    return Json::isArray($in);
}
function is_json_object($in) {
    return Json::isObject($in);
}
function is_json_struct($in) {
    return Json::isValidStruct($in);
}

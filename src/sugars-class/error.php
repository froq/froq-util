<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
declare(strict_types=1);

/**
 * Key Error.
 *
 * An error class for invalid keys (which is missing internally).
 *
 * @package froq\util
 * @object  KeyError
 * @author  Kerem Güneş
 * @since   5.25
 */
class KeyError extends Error {}

/**
 * Json Error.
 *
 * An error class for for JSONs (which is missing internally, suppose).
 *
 * @package froq\util
 * @object  KeyError
 * @author  Kerem Güneş
 * @since   6.0
 */
class JsonError extends Error {}

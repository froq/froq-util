<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
declare(strict_types=1);

/**
 * Override.
 *
 * Represents an override attribute class (for feature).
 *
 * @package froq\util
 * @object  Override
 * @author  Kerem Güneş
 * @since   6.0
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class Override
{}

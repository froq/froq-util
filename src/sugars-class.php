<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */

// Load top class files.
require __DIR__ . '/sugars-class/_error.php';
require __DIR__ . '/sugars-class/_static.php';

// Load other class files.
require __DIR__ . '/sugars-class/assert.php';
require __DIR__ . '/sugars-class/factory.php';
require __DIR__ . '/sugars-class/enum.php';
require __DIR__ . '/sugars-class/iter.php';
require __DIR__ . '/sugars-class/item.php';
require __DIR__ . '/sugars-class/json.php';
require __DIR__ . '/sugars-class/mapset.php';
require __DIR__ . '/sugars-class/mapxao.php';
require __DIR__ . '/sugars-class/options.php';
require __DIR__ . '/sugars-class/plain-array.php';
require __DIR__ . '/sugars-class/plain-object.php';
require __DIR__ . '/sugars-class/reference.php';
require __DIR__ . '/sugars-class/reflection.php';
require __DIR__ . '/sugars-class/regexp.php';
require __DIR__ . '/sugars-class/state.php';
require __DIR__ . '/sugars-class/string-buffer.php';
require __DIR__ . '/sugars-class/trace.php';
require __DIR__ . '/sugars-class/type.php';
require __DIR__ . '/sugars-class/url.php';
require __DIR__ . '/sugars-class/uuid.php';
require __DIR__ . '/sugars-class/xarray.php';
require __DIR__ . '/sugars-class/xstring.php';
require __DIR__ . '/sugars-class/xnumber.php';
require __DIR__ . '/sugars-class/xclass.php';
require __DIR__ . '/sugars-class/xobject.php';

// Extra autoload registration for "misc" classes.
spl_autoload_register(function (string $name): void {
    static $namespace = 'froq\util';

    if (str_starts_with($name, $namespace)) {
        $file = sprintf('%s/misc/%s.php', __DIR__, str_replace(
            NAMESPACE_SEPARATOR, DIRECTORY_SEPARATOR,
            substr($name, strlen($namespace) + 1)
        ));

        if (is_file($file)) {
            require $file;
        }
    }
});

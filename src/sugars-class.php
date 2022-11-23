<?php
// Load top class files.
require 'sugars-class/_error.php';
require 'sugars-class/_static.php';
require 'sugars-class/plain-object.php';
require 'sugars-class/plain-array-object.php';

// Load other class files.
require 'sugars-class/assert.php';
require 'sugars-class/factory.php';
require 'sugars-class/item.php';
require 'sugars-class/itemlist.php';
require 'sugars-class/json.php';
require 'sugars-class/locale.php';
require 'sugars-class/mapset.php';
require 'sugars-class/mapxao.php';
require 'sugars-class/options.php';
require 'sugars-class/reference.php';
require 'sugars-class/reflection.php';
require 'sugars-class/regexp.php';
require 'sugars-class/state.php';
require 'sugars-class/string-buffer.php';
require 'sugars-class/trace.php';
require 'sugars-class/type.php';
require 'sugars-class/url.php';
require 'sugars-class/uuid.php';
require 'sugars-class/xarray.php';
require 'sugars-class/xstring.php';
require 'sugars-class/xnumber.php';
require 'sugars-class/xclass.php';
require 'sugars-class/xobject.php';

// Extra autoload registration for "misc" classes.
spl_autoload_register(function (string $name): void {
    static $namespace = 'froq\util';
    if (str_starts_with($name, $namespace)) {
        $name = str_replace(
            NAMESPACE_SEPARATOR, DIRECTORY_SEPARATOR,
            substr($name, strlen($namespace) + 1)
        );
        $file = sprintf('%s/misc/%s.php', __DIR__, $name);
        if (is_file($file)) {
            require $file;
        }
    }
});

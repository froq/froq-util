<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
namespace froq\util\mapper;

use froq\util\Objects;
use froq\common\interface\Arrayable;
use froq\reflection\ReflectionType as XReflectionType;
use ReflectionObject, ReflectionClass, ReflectionProperty, ReflectionMethod;

/**
 * A mapper class, for mapping objects only.
 *
 * @package froq\util\mapper
 * @class   froq\util\mapper\Mapper
 * @author  Kerem Güneş
 * @since   6.0, 7.0
 */
class Mapper
{
    /**
     * Target object.
     */
    private object $object;

    /**
     * Mapping options.
     */
    private array $options = [
        /**
         * Array of ignored properties.
         */
        'skip' => null,

        /**
         * For undefined property errors.
         */
        'throw' => false,

        /**
         * For casting properties to their types if defined with.
         */
        'cast' => false,

        /**
         * For custom filter function skipped properties (must return false to skip).
         * @see froq\util\mapper\Mapper::isFiltered()
         */
        'filter' => null,
    ];

    /**
     * Constructor.
     *
     * @param  object|null $object
     * @param  array|null  $options
     */
    public function __construct(object $object = null, array $options = null)
    {
        $object  && $this->object  = $object;
        $options && $this->options = array_options($options, $this->options, map: false);
    }

    /**
     * Set object.
     *
     * @param  object $object
     * @return self
     */
    public function setObject(object $object): self
    {
        $this->object = $object;

        return $this;
    }

    /**
     * Get object.
     *
     * @return object|null
     */
    public function getObject(): object|null
    {
        return $this->object ?? null;
    }

    /**
     * Set option.
     *
     * @param  string $key
     * @param  mixed  $value
     * @return self
     */
    public function setOption(string $key, mixed $value): self
    {
        $this->options[$key] = $value;

        return $this;
    }

    /**
     * Get option.
     *
     * @param  string $key
     * @return mixed
     */
    public function getOption(string $key): mixed
    {
        return $this->options[$key] ?? null;
    }

    /**
     * Set var.
     *
     * @param  string      $name
     * @param  mixed       $value
     * @param  object|null $object
     * @return object
     * @causes froq\util\mapper\MapperException
     */
    public function setVar(string $name, mixed $value, object $object = null): object
    {
        return $this->map([$name => $value], $object);
    }

    /**
     * Get var.
     *
     * @param  string      $name
     * @param  object|null $object
     * @return mixed
     * @causes froq\util\mapper\MapperException
     */
    public function getVar(string $name, object $object = null): mixed
    {
        return @ $this->collect($object, $name)[$name];
    }

    /**
     * Set vars.
     *
     * @param  array       $data
     * @param  object|null $object
     * @return object
     * @causes froq\util\mapper\MapperException
     */
    public function setVars(array $data, object $object = null): object
    {
        return $this->map($data, $object);
    }

    /**
     * Get vars.
     *
     * @param  object|null $object
     * @return array
     * @causes froq\util\mapper\MapperException
     */
    public function getVars(object $object = null): array
    {
        return $this->unmap($object);
    }

    /**
     * Map given data to an object.
     *
     * Note: For after-mapping works, `onMap()` method can be defined in the source object
     * to run other jobs by your own way internally.
     *
     * @param  array       $data
     * @param  object|null $object
     * @return object
     * @causes froq\util\mapper\MapperException
     */
    public function map(array $data, object $object = null): object
    {
        // Fool update() for [] data since:
        // 1- Checking $object is done in update().
        // 2- Returning $object is byref (&) on update().
        $data = $data ?: ['' => ''];

        foreach ($data as $name => $value) {
            $this->update($object, $name, $value);
        }

        // After map, event-like method.
        if (method_exists($object, 'onMap')) {
            $object->onMap($data);
        }

        return $object;
    }

    /**
     * Collect all available properties from an object.
     *
     * @param  object|null $object
     * @return array
     * @causes froq\util\mapper\MapperException If no object given and this instance has no object yet.
     */
    public function unmap(object $object = null): array
    {
        return $this->collect($object);
    }

    /**
     * Update.
     *
     * @throws froq\util\mapper\MapperException If no object given and this instance has no object yet.
     * @throws froq\util\mapper\MapperException If `options.throw` is true for absent properties.
     * @causes froq\util\mapper\MapperException If typed / annotated property class not found or invalid.
     */
    private function update(object|null &$object, string $name, mixed $value): void
    {
        $object ??= $this->object ?? throw MapperException::forNullObject();

        // Fool update (@see map()).
        if ($name === '') {
            return;
        }

        // Skip filtered properties (if provied).
        if ($this->isFiltered($name, $value)) {
            return;
        }

        // Skip ignored properties (if provied).
        if ($this->isSkipped($name)) {
            return;
        }

        // Try with regular name.
        if (property_exists($object, $name)) {
            $this->updateProperty($object, $name, $value);
            return;
        }

        // Try with camel-case name.
        $cname = $this->getCamelCaseName($name);
        if ($cname && property_exists($object, $cname)) {
            $this->updateProperty($object, $cname, $value);
            return;
        }

        // Last tries for dynamic objects.
        if ($object instanceof \stdClass) {
            $object->$name = $value;
            return;
        }
        if ($object instanceof \ArrayAccess) {
            $object[$name] = $value;
            return;
        }

        // No such property exists.
        $this->options['throw'] && throw MapperException::forUndefinedProperty($object, $name);
    }

    /**
     * Update property.
     *
     * @throws froq\util\mapper\MapperException If typed / annotated property class not found or invalid.
     */
    private function updateProperty(object $object, string $name, mixed $value): void
    {
        // Use setter method if exists.
        if (method_exists($object, $method = 'set' . $name)) {
            $object->$method($value);
            return;
        }

        $pref = new ReflectionProperty($object, $name);

        // Skip static stuff.
        if ($pref->isStatic()) {
            return;
        }

        // Skip @hidden stuff.
        $doc = (string) $pref->getDocComment();
        if ($this->isHidden($doc)) {
            return;
        }

        $valType   = get_type($value);
        $canList   = $value && (is_iterable($value) || $valType === 'stdClass');
        $canSet    = true;
        $namespace = null;

        // Handle types.
        if ($pref->hasType()) {
            $varType = XReflectionType::from($pref->getType());
            $canSet  = $canList || $varType->contains($valType);

            if ($this->options['cast'] && $varType->isCastable()) {
                settype($value, $varType->getName());
                $canSet = true;
            } elseif ($value === null && !$varType->isNullable()) {
                $canSet = false;
            } elseif ($value !== null && !$varType->isBuiltin()) {
                $namespace = $pref->getDeclaringClass()->getNamespaceName();
                $resolved  = $this->getResolvedClass($varType, $namespace);

                if ($resolved !== null) {
                    [$class, $found] = $resolved;

                    $found || throw MapperException::forAbsentTypedPropertyClass(
                        $class, $object, property: $name
                    );

                    $vobj = $this->getClassInstance($class, $ctor);
                    $data = $this->getNormalizedData($value);

                    // With named arguments.
                    if (is_assoc_array($data)) {
                        $value = $this->map($data, $vobj);
                    }
                    // With single argument.
                    elseif ($ctor?->getNumberOfParameters() > 0) {
                        $ctor->invoke($vobj, $value);
                        $value = $vobj;
                    }

                    $canSet  = true;
                    $canList = false;
                    unset($data);
                }
            }

            if (!$canSet) {
                return;
            }
        }

        // Handle annotated lists.
        if ($canList) {
            $resolved = null;

            // From @var annotation (eg: @var Foo[]).
            if (strpos($doc, '@var')) {
                $namespace ??= $pref->getDeclaringClass()->getNamespaceName();
                $resolved    = $this->getResolvedListClass($doc, $namespace, $pref);
            } else {
                // From meta attribute (eg: #[meta(list:'Foo[]')]).
                $listClass = MapperHelper::getListClass($pref->getAttributes());
                if ($listClass !== null) {
                    $namespace ??= $pref->getDeclaringClass()->getNamespaceName();
                    $resolved    = $this->getResolvedListClass('@var ' . $listClass, $namespace, $pref, true);
                }
            }

            if ($resolved !== null) {
                foreach ($resolved as $class => $found) {
                    $found || throw MapperException::forAbsentAnnotatedPropertyClass(
                        $class, $object, property: $name
                    );
                }

                // Eg: Foo[] or array<Foo>.
                if (count($resolved) === 1) {
                    $item = $this->getClassInstance($class);
                    $vals = [];  // Will re-list value items.

                    foreach ($this->getNormalizedData($value) as $data) {
                        if (is_assoc_array($data)) {
                            $vals[] = $this->map($data, clone $item);
                        } else {
                            $data = $this->getNormalizedData($data);
                            if (is_assoc_array($data)) {
                                $vals[] = $this->map($data, clone $item);
                            }
                        }
                    }

                    $value = $vals;
                } else {
                    // Eg: FooList<Foo>.
                    [$listClass, $itemClass] = array_keys($resolved);

                    $item = $this->getClassInstance($itemClass);
                    $vals = $this->getClassInstance($listClass);

                    foreach ($this->getNormalizedData($value) as $data) {
                        if (is_assoc_array($data)) {
                            $vals[] = $this->map($data, clone $item);
                        } else {
                            $data = $this->getNormalizedData($data);
                            if (is_assoc_array($data)) {
                                $vals[] = $this->map($data, clone $item);
                            }
                        }
                    }

                    $value = $vals;
                }
            }
        }

        $pref->setValue($object, $value);
    }

    /**
     * Collect.
     *
     * @throws froq\util\mapper\MapperException If no object given and this instance has no object yet.
     * @throws froq\util\mapper\MapperException If name given but not found in collected properties.
     */
    private function collect(object|null $object, string $name = null): array
    {
        $object ??= $this->object ?? throw MapperException::forNullObject();

        // Use getter method if exists.
        if ($name !== null && method_exists($object, $method = 'get' . $name)) {
            return $object->$method();
        }

        $data = [];
        $oref = new ReflectionObject($object);

        // Resource friendly.
        if ($name === null) {
            $prefs = $oref->getProperties();
        } else {
            $prefs = [];
            if ($oref->hasProperty($name)) {
                $prefs = [$name => $oref->getProperty($name)];
            }
        }

        foreach ($prefs as $pref) {
            // Single property wanted.
            if ($name !== null && $name !== $pref->name) {
                continue;
            }

            // Skip static stuff.
            if ($pref->isStatic()) {
                continue;
            }

            // Skip @hidden stuff.
            $doc = (string) $pref->getDocComment();
            if ($this->isHidden($doc)) {
                continue;
            }

            // Skip filtered properties (if provied).
            if ($this->isFiltered($pref->name, $value)) {
                continue;
            }

            // Skip ignored properties (if provied).
            if ($this->isSkipped($pref->name)) {
                continue;
            }

            $data[$pref->name] = $pref->isInitialized($object) ? $pref->getValue($object) : null;
        }

        // Try to collect array-like object.
        $data = $data ?: $this->collectData($object);

        // No such property exists.
        if ($name !== null && !array_key_exists($name, $data)) {
            $this->options['throw'] && throw MapperException::forUndefinedProperty($object, $name);
        }

        return $data;
    }

    /**
     * Collect data from an array-like object.
     */
    private function collectData(object $object): array
    {
        switch (true) {
            case ($object instanceof Arrayable):
                return $object->toArray();
            case ($object instanceof \Traversable):
                return [...$object];
            case ($object instanceof \IteratorAggregate):
                return [...$object->getIterator()];
            case ($object instanceof \stdClass):
                return get_object_vars($object);
            default:
                return [];
        }
    }

    /**
     * Get creating a new instance from given class that resolved as property type.
     *
     * Note: If the given class's constructor has required parameters, constructor will not be
     * called. So, for constructor related works, `onMap()` method must be defined in the source
     * class.
     */
    private function getClassInstance(string $class, ReflectionMethod &$ctor = null): object
    {
        $cref = new ReflectionClass($class);
        $ctor = $cref->getConstructor();

        return (!$ctor || $ctor->getNumberOfRequiredParameters() > 0)
             ? $cref->newInstanceWithoutConstructor()
             : $cref->newInstance();
    }

    /**
     * Get resolving class from given property type.
     *
     * @return array<string: class-name, bool: found-state>|null
     */
    private function getResolvedClass(XReflectionType $tref, string $namespace): array|null
    {
        $resolved = $this->resolveClass($tref, $namespace);

        return $resolved;
    }

    /**
     * Get resolving list class from given property annotation / attribute.
     *
     * Example: `Foo[]`, `array<Foo>` or `ItemList<Foo>` (same namespace).
     * Example: `acme\Foo[]`, `array<acme\Foo>` or `ItemList<acme\Foo>` (fully qualified).
     *
     * Note: Complex (union, intersection) types are not supported by this method. So, setter
     * methods should be used for these kind of types. Also, all hinted types must either be
     * in the same namespace of the mapped object or be typed as fully qualified in place.
     *
     * @return array<string: class-name, bool: found-state, ...>|null
     * @causes froq\util\mapper\MapperException If annotation / attribute is like: 0[], 0<>, Foo<>, Foo<0> or int<>.
     */
    private function getResolvedListClass(string $doc, string $namespace, ReflectionProperty $pref, bool $isAttr = false): array|null
    {
        $type = grep('~@var +(?:(.+)<(.*)>|(.+)\[\])~', $doc);

        if ($type !== null) {
            $meta = [$doc, 'type' => $isAttr ? 'attribute' : 'annotation'];

            // Valid: array<Foo>, invalid: int<Foo>.
            if (is_array($type) && strpbrk($doc, '<>')) {
                $tref = new XReflectionType($type[0]);
                if ($tref->isBuiltin() && !$tref->equals(['array', 'iterable'])) {
                    $this->validateClass('', $meta, $pref); // '' is for error.
                }
            }

            // Drop "array" part (eg: array<Foo>).
            if (is_array($type) && in_array($type[0], ['array', 'iterable'])) {
                $type = $type[1];
            }

            $resolved = null;

            // Eg: Foo[] or array<Foo>
            if (is_string($type)) {
                $this->validateClass($type, $meta, $pref);

                if ($resolved = $this->resolveClass($type, $namespace)) {
                    [$class, $found] = $resolved;
                    $resolved = [$class => $found];
                }
            }
            // Eg: FooList<Foo>
            else {
                $this->validateClass($type[0], $meta, $pref);
                $this->validateClass($type[1], $meta, $pref);

                foreach ([
                    $this->resolveClass($type[0], $namespace), // List class.
                    $this->resolveClass($type[1], $namespace), // Item class.
                ] as [$class, $found]) {
                    $resolved[$class] = $found;
                }
            }

            return $resolved;
        }

        return null;
    }

    /**
     * Resolve a class (type) or return null if no such valid class available.
     *
     * @return array<string: class-name, bool: found-state>|null
     */
    private function resolveClass(string|XReflectionType $type, string $namespace): array|null
    {
        $tref = is_string($type) ? new XReflectionType($type) : $type;
        $tkey = $tref->getName();

        static $resolve, $resolves;

        if (isset($resolves[$tkey])) {
            return $resolves[$tkey];
        }

        // Noop..
        if ($tref->isBuiltin()) {
            return ($resolves[$tkey] = null);
        }

        // Resolver macro (memoized).
        $resolve ??= function (string $name, ?string $namespace): ?string {
            if (class_exists($name)) {
                return $name;
            }
            if ($namespace && class_exists($namespace . '\\' . $name)) {
                return $namespace . '\\' . $name;
            }
            return null;
        };

        // Single classes.
        if ($tref->isClass()) {
            $name = $tref->getPureName();
            if ($class = $resolve($name, $namespace)) {
                return ($resolves[$tkey] = [$class, true]);
            }
        }

        // Choose a class (for unions).
        foreach ($tref->getNames() as $name) {
            if ($class = $resolve($name, $namespace)) {
                return ($resolves[$tkey] = [$class, true]);
            }

            // Special case of date/time stuff (interface, subclass or union).
            if (equals($name, 'DateTime', 'DateTimeImmutable', 'DateTimeInterface')) {
                return is_subclass_of($name, 'DateTimeInterface')
                     ? ($resolves[$tkey] = [$name     , true])  // DateTime, DateTimeImmutable.
                     : ($resolves[$tkey] = ['DateTime', true]); // DateTimeInterface.
            }
        }

        $name = $tref->getPureName() ?: $tref->getName();
        return ($resolves[$tkey] = [$name, false]);
    }

    /**
     * Validate a class name or throw a `MapperException` for invalid annotation / attribute.
     *
     * @throws froq\util\mapper\MapperException
     */
    private function validateClass(string $class, array $meta, ReflectionProperty $pref): void
    {
        preg_test('~^([\\\]?[a-z_][a-z0-9_\\\]*)$~i', $class)
            || throw MapperException::forInvalidMeta($meta, $pref->class, $pref->name);
    }

    /**
     * Get normalizing data for values to use in sub-mappings.
     */
    private function getNormalizedData(mixed $value): array
    {
        switch (true) {
            case is_array($value):
                return $value;
            case is_iterable($value):
                return [...$value];
            case ($value instanceof \stdClass):
                return get_object_vars($value);
            default:
                return [];
        }
    }

    /**
     * Get camel-case name if name contains "_-" characters.
     */
    private function getCamelCaseName(string $name): string|null
    {
        if (strpbrk($name, '_-') === false) {
            return null;
        }

        // Eg: foo_bar or foo-bar => fooBar.
        return preg_replace_callback(
            '~(?<![_-])[_-]([a-z0-9]{1})~',
            fn($m): string => ucfirst($m[1]),
            $name
        );
    }

    /**
     * Check whether a property marked as filtered by `filter` option.
     */
    private function isFiltered(string $name, mixed $value): bool
    {
        return ($filter = $this->options['filter']) && !$filter($name, $value);
    }

    /**
     * Check whether a property marked as skipped by `skip` option.
     */
    private function isSkipped(string $name): bool
    {
        return ($skip = $this->options['skip']) && in_array($name, $skip, true);
    }

    /**
     * Check whether a property marked as hidden by `@hidden` annotation tag.
     */
    private function isHidden(string $doc): bool
    {
        return $doc && strpos($doc, '@hidden');
    }
}

<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
declare(strict_types=1);

use froq\util\Objects;

/*** Type stuff. ***/

/**
 * An extended ReflectionType class that combines ReflectionNamedType, ReflectionUnionType
 * and ReflectionIntersectionType as one class.
 *
 * @package froq\util
 * @object  ReflectionTypeExtended
 * @author  Kerem Güneş
 * @since   5.31
 */
class ReflectionTypeExtended extends ReflectionType
{
    /** Name/nullable reference. */
    private object $reference;

    /** Typing delimiter. */
    private string $delimiter;

    /**
     * Constructor.
     *
     * @param  string $name
     * @param  bool   $nullable
     * @throws ReflectionException
     */
    public function __construct(string $name, bool $nullable = false)
    {
        $name || throw new ReflectionException('No name given');

        // Null/mixed is nullable.
        if ($name == 'null' || $name == 'mixed') {
            $nullable = true;
        }

        // Uniform nullable types.
        if (strpfx($name, '?')) {
            $name = substr($name, 1);
            $nullable = true;
        }

        // @tome: Null allways goes to the end.
        if ($nullable && ($name != 'null' && $name != 'mixed')) {
            $name .= '|null';
        }

        // @tome: Intersection-type not allows nulls.
        $this->delimiter = str_contains($name, '&') ? '&' : '|';

        $name = implode($this->delimiter, array_unique(explode($this->delimiter, $name)));

        $this->reference = qo(name: $name, nullable: $nullable);
    }

    /**
     * Proxy for reference object properties.
     *
     * @param  string $property
     * @return string
     * @throws Error
     * @magic
     */
    public function __get(string $property): string|bool
    {
        if (equal($property, 'name', 'nullable')) {
            return $this->reference->$property;
        }

        throw new Error(sprintf(
            'Undefined property %s::$%s',
            $this::class, $property
        ));
    }

    /** @magic */
    public function __debugInfo(): array
    {
        return ['name' => $this->reference->name,
                'nullable' => $this->reference->nullable];
    }

    /** @magic */
    public function __toString(): string
    {
        return $this->reference->name;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->reference->name;
    }

    /**
     * Get pure name if named (without "null" part if nullable).
     *
     * @return string|null
     */
    public function getPureName(): string|null
    {
        if ($this->isNamed()) {
            return first($this->getNames());
        }
        return null;
    }

    /**
     * Get names.
     *
     * @return array
     */
    public function getNames(): array
    {
        return explode($this->delimiter, $this->reference->name);
    }

    /**
     * Get types.
     *
     * @return array<ReflectionTypeExtended>
     */
    public function getTypes(): array
    {
        return array_map(fn($name) => new ReflectionTypeExtended($name),
            $this->getNames());
    }

    /**
     * Builtin state checker.
     *
     * @return bool
     */
    public function isBuiltin(): bool
    {
        return preg_test('~^(int|float|string|bool|array|object|callable|iterable|mixed)(\|null)?$~',
            $this->getName());
    }

    /**
     * Named-type state checker.
     *
     * @return bool
     */
    public function isNamed(): bool
    {
        return !$this->isUnion() && !$this->isIntersection();
    }

    /**
     * Union-type state checker.
     *
     * @return bool
     */
    public function isUnion(): bool
    {
        return substr_count($this->getName(), '|') >= 2;
    }

    /**
     * Intersection-type state checker.
     *
     * @return bool
     */
    public function isIntersection(): bool
    {
        return substr_count($this->getName(), '&') >= 1;
    }

    /**
     * Nullable state checker.
     *
     * @return bool
     */
    public function isNullable(): bool
    {
        return $this->reference->nullable;
    }

    /** @alias isNullable() */
    public function allowsNull(): bool
    {
        return $this->reference->nullable;
    }

    /**
     * Check whether contains given type name.
     *
     * @param  string $name
     * @return bool
     */
    public function contains(string $name): bool
    {
        return in_array($name, $this->getNames(), true);
    }
}

/*** Parameter stuff. ***/

/**
 * An extended ReflectionParameter class.
 *
 * @package froq\util
 * @object  ReflectionParameterExtended
 * @author  Kerem Güneş
 * @since   5.31
 */
class ReflectionParameterExtended extends ReflectionParameter
{
    /**
     * Check default value existence.
     *
     * @return bool
     */
    public function hasDefaultValue(): bool
    {
        return parent::isDefaultValueAvailable()
            || $this->isDefaultValueConstant();
    }

    /**
     * Get default value return null.
     *
     * @return mixed
     */
    public function getDefaultValue(): mixed
    {
        if (parent::isDefaultValueAvailable()) {
            return parent::getDefaultValue();
        } elseif ($this->isDefaultValueConstant()) {
            return parent::getDefaultValueConstantName();
        }
        return null;
    }

    /** @override */
    public function isDefaultValueConstant(): bool
    {
        // Handle: "Internal error: Failed to retrieve the default value ..." error.
        try {
            return parent::isDefaultValueConstant();
        } catch (Throwable $e) {
            return false;
        }
    }

    /** @override */
    public function getType(): ReflectionTypeExtended|null
    {
        if ($type = parent::getType()) {
            return new ReflectionTypeExtended(
                ($type instanceof ReflectionNamedType) ? $type->getName() : (string) $type,
                $type->allowsNull()
            );
        }
        return null;
    }
}

/*** Class stuff. ***/

/**
 * An extended ReflectionClass class.
 *
 * @package froq\util
 * @object  ReflectionClassExtended
 * @author  Kerem Güneş
 * @since   5.31
 */
class ReflectionClassExtended extends ReflectionClass
{
    use ReflectionClassTrait;
}

/**
 * An extended ReflectionObject class.
 *
 * @package froq\util
 * @object  ReflectionObjectExtended
 * @author  Kerem Güneş
 * @since   5.31
 */
class ReflectionObjectExtended extends ReflectionObject
{
    use ReflectionClassTrait;
}

/**
 * Kind of missing classes, ReflectionTrait/ReflectionInterface.
 *
 * @package froq\util
 * @object  ReflectionTrait,ReflectionInterface
 * @author  Kerem Güneş
 * @since   5.31
 */
class ReflectionTrait extends ReflectionClassExtended {}
class ReflectionInterface extends ReflectionClassExtended {}

/**
 * A trait that used by ReflectionClassExtended & ReflectionObjectExtended classes.
 *
 * @package froq\util
 * @object  ReflectionCallableTrait
 * @author  Kerem Güneş
 * @since   5.27
 */
trait ReflectionClassTrait
{
    /** Class/object reference. */
    private string|object $reference;

    /**
     * Constructor.
     *
     * @param string|object $class
     */
    public function __construct(string|object $class)
    {
        $this->reference = $class;

        parent::__construct($class);
    }

    /** @magic */
    public function __debugInfo(): array
    {
        return ['name' => $this->name];
    }

    /**
     * Type.
     *
     * @return string
     */
    public function type(): string
    {
        return match (true) {
            $this->isInterface() => 'interface',
            $this->isTrait()     => 'trait',
            default              => 'class'
        };
    }

    /** @missing */
    public function isClass(): bool
    {
        return !$this->isInterface() && !$this->isTrait() && !$this->isEnum();
    }

    /**
     * Set of methods.
     *
     * @return Set<ReflectionMethodExtended>
     */
    public function methods(bool $map = true): Set
    {
        return Set::from(parent::getMethods())
            ->map(fn($ref) => new ReflectionMethodExtended($ref->class, $ref->name));
    }

    /** @override */ #[ReturnTypeWillChange]
    public function getMethod(string $name): ReflectionMethodExtended|null
    {
        return parent::hasMethod($name) && ($method = parent::getMethod($name))
             ? new ReflectionMethodExtended($method->class, $name)
             : null;
    }

    /** @override */
    public function getMethods(int $filter = null): array
    {
        return array_map(fn($ref) => new ReflectionMethodExtended($ref->class, $ref->name),
            parent::getMethods($filter));
    }

    /**
     * Get method names.
     *
     * @param  int|null $filter
     * @return array<string|null>
     */
    public function getMethodNames(int $filter = null): array
    {
        return array_map(fn($ref) => $ref->name, parent::getMethods($filter));
    }

    /**
     * Get namespace with/without base-only option.
     *
     * @param  bool $baseOnly
     * @return string
     */
    public function getNamespace(bool $baseOnly = false): string
    {
        return $baseOnly ? Objects::getNamespace($this->reference, true) : parent::getNamespaceName();
    }

    /** @alias getParent() @override */ #[ReturnTypeWillChange]
    public function getParentClass(): ReflectionClassExtended|null
    {
        return $this->getParent();
    }

    /** @alias getParents() */
    public function getParentClasses(): array
    {
        return $this->getParents();
    }

    /**
     * Set of parents.
     *
     * @return Set
     */
    public function parents(): Set
    {
        return new Set(Objects::getParents($this->reference));
    }

    /**
     * Get parent.
     *
     * @return ReflectionClassExtended|null
     */
    public function getParent(): ReflectionClassExtended|null
    {
        return ($parent = parent::getParentClass())
             ? new ReflectionClassExtended($parent->name) : null;
    }

    /**
     * Get parents.
     *
     * @return array<ReflectionClassExtended|null>
     */
    public function getParents(): array
    {
        return $this->parents()->map(fn($name) => new ReflectionClassExtended($name))
            ->toArray();
    }

    /**
     * Get parent class names.
     *
     * @return array<string|null>
     */
    public function getParentNames(): array
    {
        return $this->parents()->toArray();
    }

    /**
     * Set of interfaces.
     *
     * @return Set
     */
    public function interfaces(): Set
    {
        return new Set(Objects::getInterfaces($this->reference));
    }

    /**
     * Get interface.
     *
     * @param  string $name
     * @return ReflectionInterface|null
     */
    public function getInterface(string $name): ReflectionInterface|null
    {
        return $this->interfaces()->has($name) ? new ReflectionInterface($name) : null;
    }

    /** @override */
    public function getInterfaces(): array
    {
        return $this->interfaces()->map(fn($name) => new ReflectionInterface($name))
            ->toArray();
    }

    /** @override */
    public function getInterfaceNames(): array
    {
        return $this->interfaces()->toArray();
    }

    /**
     * Set of traits.
     *
     * @return Set
     */
    public function traits(): Set
    {
        return new Set(Objects::getTraits($this->reference, all: true));
    }

    /**
     * Get trait.
     *
     * @param  string $name
     * @return ReflectionTrait|null
     */
    public function getTrait(string $name): ReflectionTrait|null
    {
        return $this->traits()->has($name) ? new ReflectionTrait($name) : null;
    }

    /** @override */
    public function getTraits(): array
    {
        return $this->traits()->map(fn($name) => new ReflectionTrait($name))
            ->toArray();
    }

    /** @override */
    public function getTraitNames(): array
    {
        return $this->traits()->toArray();
    }

    /**
     * Map of attributes.
     *
     * @return Map<ReflectionAttribute>
     */
    public function attributes(): Map
    {
        return Map::from($this->getAttributes());
    }

    /**
     * Has attribute.
     *
     * @param  string $name
     * @return bool
     */
    public function hasAttribute(string $name): bool
    {
        return $this->getAttribute($name) != null;
    }

    /**
     * Get attribute.
     *
     * @param  string $name
     * @return ReflectionAttribute|null
     */
    public function getAttribute(string $name): ReflectionAttribute|null
    {
        return $this->attributes()->find(fn($ref) => $ref->getName() == $name);
    }

    /**
     * Get attribute names.
     *
     * @return array
     */
    public function getAttributeNames(): array
    {
        return $this->attributes()->map(fn($ref) => $ref->getName())->toArray();
    }

    /**
     * Set of properties.
     *
     * @return Set<ReflectionPropertyExtended>
     */
    public function properties(): Set
    {
        return new Set($this->collectProperties(extend: true));
    }

    /** @override */
    public function hasProperty(string $name): bool
    {
        if (is_object($this->reference)
            && (isset($this->reference->$name) ||
                property_exists($this->reference, $name))) {
            return true;
        }

        return $this->collectProperties(name: $name)
            ->has($name);
    }

    /** @override */ #[ReturnTypeWillChange]
    public function getProperty(string $name): ReflectionPropertyExtended|null
    {
        if (is_object($this->reference)
            && (isset($this->reference->$name) ||
                property_exists($this->reference, $name))) {
            return new ReflectionPropertyExtended($this->reference, $name);
        }

        return $this->collectProperties(name: $name, extend: true)
            ->get($name);
    }

    /** @override */
    public function getProperties(int $filter = null): array
    {
        return $this->collectProperties($filter, extend: true)
            ->values();
    }

    /**
     * Get property names.
     *
     * @param  int|null $filter
     * @return array<string|null>
     */
    public function getPropertyNames(int $filter = null): array
    {
        return $this->collectProperties($filter)
            ->keys();
    }

    /**
     * Get property values.
     *
     * @param  int|null $filter
     * @param  bool     $assoc
     * @return array<mixed|null>
     */
    public function getPropertyValues(int $filter = null, bool $assoc = false): array
    {
        $map = $this->collectProperties($filter, extend: true, fill: true);
        return $assoc ? $map->toArray() : $map->values();
    }

    /**
     * Collect properties, extend if requested.
     */
    private function collectProperties(int $filter = null, bool $fill = false, bool $extend = false, string $name = null): Map
    {
        $map = new Map();

        foreach (parent::getProperties($filter) as $property) {
            // When single one wanted.
            if ($name && $name != $property->name) {
                continue;
            }
            $map[$property->name] = $property->name;
        }

        // No public vanted?
        if ($filter && !($filter & ReflectionProperty::IS_PUBLIC)) {
            // Extend with instances & values optionally.
            $extend && ($map = $this->extendProperties($map, $fill, $name));

            return $map;
        }

        // Dynamic properties.
        if (is_object($this->reference) && ($vars = get_object_vars($this->reference))) {
            $vnames = array_merge($map->keys(), array_keys($vars));
            foreach ($vnames as $vname) {
                // When single one wanted.
                if ($name && $name != $vname) {
                    continue;
                }
                $map[$vname] || ($map[$vname] = $vname);
            }
        }

        $inames = [];

        // Special weirdos, they don't provide their precious properties for reflection.
        if ($this->isInternal() && is_equal_of($this->name,
            'ArrayObject', 'ArrayIterator', 'SplObjectStorage',
            'SplMaxHeap', 'SplMinHeap', 'SplPriorityQueue',
            'SplDoublyLinkedList', 'SplStack', 'SplQueue',
        )) {
            $thisName = $this->name;
            if (is_equal_of($thisName, 'ArrayObject', 'ArrayIterator', 'SplObjectStorage')) {
                array_push($inames, 'storage');
            } elseif (is_equal_of($thisName, 'SplMaxHeap', 'SplMinHeap', 'SplPriorityQueue')) {
                array_push($inames, 'flags', 'isCorrupted', 'heap');
            } elseif (is_equal_of($thisName, 'SplDoublyLinkedList', 'SplStack', 'SplQueue')) {
                array_push($inames, 'flags', 'dllist');
            }
        }
        else {
            $thisClass = $this->reference;
            // DOM stuff, yes..
            if (is_class_of($thisClass, 'DOMNode')) {
                $tmp1 = [];
                array_append($tmp1, 'nodeName', 'nodeValue', 'nodeType', 'parentNode', 'childNodes', 'firstChild',
                    'lastChild', 'previousSibling', 'nextSibling', 'attributes', 'ownerDocument', 'namespaceURI',
                    'prefix', 'localName', 'baseURI', 'textContent');
                // Extenders.
                switch (true) {
                    case is_class_of($thisClass, 'DOMDocument'):
                        array_prepend($tmp1, 'actualEncoding', 'config', 'doctype', 'documentElement', 'documentURI',
                            'encoding', 'formatOutput', 'implementation', 'preserveWhiteSpace', 'recover', 'resolveExternals',
                            'standalone', 'strictErrorChecking', 'substituteEntities', 'validateOnParse', 'version', 'xmlEncoding',
                            'xmlStandalone', 'xmlVersion', 'firstElementChild', 'lastElementChild', 'childElementCount');
                        break;
                    case is_class_of($thisClass, 'DOMElement'):
                        array_prepend($tmp1, 'schemaTypeInfo', 'tagName');
                        break;
                    case is_class_of($thisClass, 'DOMAttr'):
                        array_prepend($tmp1, 'name', 'ownerElement', 'schemaTypeInfo', 'specified', 'value');
                        break;
                    case is_class_of($thisClass, 'DOMProcessingInstruction'):
                        array_prepend($tmp1, 'target', 'data');
                        break;
                    // Mutual property holders (DOMCharacterData > DOMText, DOMComment, DOMCDataSection).
                    case is_class_of($thisClass, 'DOMCharacterData'):
                        array_prepend($tmp1, 'data', 'length');
                        if (is_class_of($thisClass, 'DOMText')) {
                            array_prepend($tmp1, 'wholeText');
                        }
                        break;
                    // Mutual property holders (DOMNotation, DOMEntity, DOMDocumentType).
                    case is_class_of($thisClass, 'DOMNotation', 'DOMEntity', 'DOMDocumentType'):
                        $tmp2 = ['publicId', 'systemId'];
                        if (is_class_of($thisClass, 'DOMEntity')) {
                            array_append($tmp2, 'notationName', 'actualEncoding', 'encoding', 'version');
                        } elseif (is_class_of($thisClass, 'DOMDocumentType')) {
                            array_append($tmp2, 'name', 'entities', 'notations', 'internalSubset');
                        }
                        $tmp1 = array_merge($tmp2, $tmp1);
                        break;
                }
                $inames = array_merge($inames, $tmp1);
            } elseif (is_class_of($thisClass, 'DOMNodeList', 'DOMNamedNodeMap')) {
                array_push($inames, 'length');
            } elseif (is_class_of($thisClass, 'DOMXPath')) {
                array_push($inames, 'document');
            }
            // Date/Time stuff.
            elseif (is_class_of($thisClass, 'DateTime', 'DateTimeImmutable')) {
                array_push($inames, 'date', 'timezone', 'timezone_type');
            } elseif (is_class_of($thisClass, 'DateTimeZone')) {
                array_push($inames, 'timezone', 'timezone_type');
            }
        }

        // Add internals.
        foreach ($inames as $iname) {
            // When single one wanted.
            if ($name && $name != $iname) {
                continue;
            }
            $map[$iname] || ($map[$iname] = $iname);
        }

        // Extend with instances & values optionally.
        $extend && ($map = $this->extendProperties($map, $fill, $name));

        return $map;
    }

    /**
     * Extend properties, add values if requested.
     */
    private function extendProperties(Map $map, bool $fill = false): Map
    {
        foreach ($map->keys() as $pname) {
            $property = new ReflectionPropertyExtended($this->reference, $pname);

            // Fill with value.
            $map[$pname] = $fill ? $property->getValue() : $property;
        }

        return $map;
    }
}

/*** Property stuff. ***/

/**
 * An extended ReflectionProperty class.
 *
 * @package froq\util
 * @object  ReflectionPropertyExtended
 * @author  Kerem Güneş
 * @since   5.31
 */
class ReflectionPropertyExtended extends ReflectionProperty
{
    /** Property reference */
    private object $reference;

    /**
     * Constructor.
     *
     * @param string|object $class
     * @param string        $name
     */
    public function __construct(string|object $class, string $name)
    {
        $this->reference = qo(name: $name, class: $class);
        // Add owner as a cached declaring class.
        $this->reference->owner = $this->getDeclaringClass();

        // Handle internal (non-reflectable) weirdos.
        try {
            parent::__construct($class, $name);
        } catch (Throwable $e) {
            // Handle non-existing property but non-internal related errors.
            preg_match('~\s+(.+)::(\$'. $name .')\s+~', $e->getMessage(), $match);
            if ($match && !$this->isInternal() && !$this->inInternals($class, $name)) {
                throw new ReflectionException(sprintf(
                    'Property %s::%s does not exist', $match[1], $match[2]
                ));
            }
        }
    }

    /** @magic */
    public function __debugInfo(): array
    {
        return ['name' => $this->getName(), 'class' => $this->getClass(),
                'type' => $this->getType()?->getName()];
    }

    /** @magic */
    public function __toString(): string
    {
        try {
            return parent::__toString();
        } catch (Throwable) {
            $tmp = ['Property ['];

            if ($this->isDynamic()) {
                $tmp[] = '<dynamic>';
            } elseif ($this->isInternal()) {
                $tmp[] = '<internal>';
            }

            $tmp[] = join(' ', $this->getModifierNames());
            if ($type = $this->getType()) {
                $tmp[] = $type;
            }

            $tmp[] = '$'. $this->getName();
            $tmp[] = ']';

            return join(' ', $tmp);
        }
    }

    /**
     * @note Instead of "$ref->name" this must be called.
     * @override
     */
    public function getName(): string
    {
        return $this->reference->name;
    }

    /**
     * @note Instead of "$ref->class" this must be called.
     * @override
     */
    public function getClass(): string
    {
        return $this->reference->owner->name;
    }

    /** @override */
    public function getDeclaringClass(): ReflectionClassExtended
    {
        if (isset($this->reference->owner)) {
            return $this->reference->owner;
        }

        // Handle "Internal error: Failed to retrieve the reflection object" and others.
        try {
            return new ReflectionClassExtended(parent::getDeclaringClass()->name);
        } catch (Throwable) {
            $parents = (new ReflectionClassExtended($this->reference->class))->getParentClasses();

            while ($parents) {
                // Pop for reverse order.
                $parent = array_pop($parents);

                if ($parent->hasProperty($this->reference->name)) {
                    $property = $parent->getProperty($this->reference->name);
                    // Return real owner.
                    if (!$property->isPrivate()) {
                        return $parent;
                    }
                }
            }

            return new ReflectionClassExtended($this->reference->class);
        }
    }

    /**
     * Set of traits.
     *
     * @return Set
     */
    public function traits(): Set
    {
        return Set::from($this->getDeclaringClass()->getTraits())
            ->filter(fn($ref) => (
                $ref->hasProperty($this->reference->name) &&
                $ref->getProperty($this->reference->name)->class == $ref->name
            ));
    }

    /**
     * Get traits.
     *
     * @return array<ReflectionTrait|null>
     */
    public function getTraits(): array
    {
        return $this->traits()->toArray();
    }

    /**
     * Get trait.
     *
     * @param  string|null $name
     * @return ReflectionTrait|null
     */
    public function getTrait(string $name = null): ReflectionTrait|null
    {
        return ($name == null) ? $this->traits()->last()
             : $this->traits()->find(fn($ref) => $ref->name == $name);
    }

    /**
     * Get trait names.
     *
     * @return array<string|null>
     */
    public function getTraitNames(): array
    {
        return $this->traits()->map(fn($ref) => $ref->name)->toArray();
    }

    /**
     * Map of attributes.
     *
     * @return Map<ReflectionAttribute>
     */
    public function attributes(): Map
    {
        return Map::from($this->getAttributes());
    }

    /**
     * Has attribute.
     *
     * @param  string $name
     * @return bool
     */
    public function hasAttribute(string $name): bool
    {
        return $this->getAttribute($name) != null;
    }

    /**
     * Get attribute.
     *
     * @param  string $name
     * @return ReflectionAttribute|null
     */
    public function getAttribute(string $name): ReflectionAttribute|null
    {
        return $this->attributes()->find(fn($ref) => $ref->getName() == $name);
    }

    /**
     * Get attribute names.
     *
     * @return array
     */
    public function getAttributeNames(): array
    {
        return $this->attributes()->map(fn($ref) => $ref->getName())->toArray();
    }

    /** @override */
    public function setValue(mixed $object = null, mixed $value = null): void
    {
        // Cannot override orgiginal setValue().
        if (func_num_args() == 1) {
            [$value, $object] = [$object, null];
        }

        $object ??= $this->reference->class;

        if (is_object($object)) {
            // Handle "Internal error: Failed to retrieve the reflection object" and others.
            try {
                parent::setValue($object, $value);
            } catch (Throwable) {
                $name = $this->reference->name;

                // Check & throw original exception.
                if (!is_class_of($object, $this->reference->owner->name)) {
                    throw new ReflectionException(sprintf(
                        'Given object is not an instance of the class this property '.
                        '$%s was declared in', $name
                    ));
                }

                // Sorry..
                if ($this->isPublic() || $this->isDynamic()) {
                    $object->$name = $value;
                }
            }
        } else {
            throw new ReflectionException(sprintf(
                'Cannot set property $%s of non-instantiated class',
                $this->reference->name, get_class_name($this->reference->class)
            ));
        }
    }

    /** @override */
    public function getValue(object $object = null): mixed
    {
        $value = null;
        $object ??= $this->reference->class;

        if (is_object($object)) {
            // Handle "Internal error: Failed to retrieve the reflection object" and others.
            try {
                $value =@ parent::getValue($object);
            } catch (Throwable) {
                $name = $this->reference->name;

                // Check & throw original exception.
                if (!is_class_of($object, $this->reference->owner->name)) {
                    throw new ReflectionException(sprintf(
                        'Given object is not an instance of the class this property '.
                        '$%s was declared in', $name
                    ));
                }

                $value = $this->peekValue($object, $name)[0];
            }
        } else {
            // Handle "Internal error: Failed to retrieve the reflection object" and others.
            try {
                if (parent::hasDefaultValue()) {
                    $value = parent::getDefaultValue();
                }
            } catch (Throwable) {}
        }

        return $value;
    }

    /** @override */
    public function hasDefaultValue(): bool
    {
        // Handle "Internal error: Failed to retrieve the reflection object" and others.
        try {
            return parent::hasDefaultValue();
        } catch (Throwable) {
            return $this->peekValue(default: true)[1];
        }
    }

    /** @override */
    public function getDefaultValue(): mixed
    {
        // Handle "Internal error: Failed to retrieve the reflection object" and others.
        try {
            if (parent::hasDefaultValue()) {
                return parent::getDefaultValue();
            }
        } catch (Throwable) {
            return $this->peekValue(default: true)[0];
        }
    }

    /** @override */
    public function hasType(): bool
    {
        return $this->getType() != null;
    }

    /** @override */
    public function getType(): ReflectionTypeExtended|null
    {
        return ($res = $this->resolveType()) ? new ReflectionTypeExtended(...$res) : null;
    }

    /**
     * Get visibility.
     *
     * @return string
     */
    public function getVisibility(): string
    {
        return $this->isPublic() ? 'public' : ($this->isPrivate() ? 'private' : 'protected');
    }
    /**
     * Get modifier names.
     *
     * @return array
     */
    public function getModifierNames(): array
    {
        return Reflection::getModifierNames($this->getModifiers());
    }

    /** @override */
    public function getModifiers(): int
    {
        return $this->callOverridingMethod('getModifiers', [], function () {
            $modifiers = 0;

            $this->isPublic()    && $modifiers |= ReflectionProperty::IS_PUBLIC;
            $this->isPrivate()   && $modifiers |= ReflectionProperty::IS_PRIVATE;
            $this->isProtected() && $modifiers |= ReflectionProperty::IS_PROTECTED;
            $this->isStatic()    && $modifiers |= ReflectionProperty::IS_STATIC;
            $this->isReadOnly()  && $modifiers |= ReflectionProperty::IS_READONLY;

            return $modifiers;
        });
    }

    /** @override */ #[ReturnTypeWillChange]
    public function getDocComment(): string|null
    {
        return $this->callOverridingMethod('getDocComment', [], null);
    }

    /** @override */
    public function getAttributes(string $name = null, int $flags = 0): array
    {
        return $this->callOverridingMethod('getAttributes', [$name, $flags], []);
    }

    /** @override */
    public function isDefault(): bool
    {
        return $this->callOverridingMethod('isDefault', [], fn() => !$this->isDynamic());
    }

    /** @override */
    public function isInitialized(object $object = null): bool
    {
        return $this->callOverridingMethod('isInitialized', [], fn() => (
            $this->isDynamic() || $this->getValue($object) !== null
        ));
    }

    /** @override */
    public function isPublic(): bool
    {
        return $this->callOverridingMethod('isPublic', [], fn() => (
            $this->isDynamic() || in_array($this->getName(), $this->getDomInternals())
        ));
    }

    /** @override */
    public function isPrivate(): bool
    {
        return $this->callOverridingMethod('isPrivate', [], fn() => (
            !$this->isPublic() && in_array($this->getName(), $this->getInternals())
        ));
    }

    /** @override */
    public function isProtected(): bool
    {
        return $this->callOverridingMethod('isProtected', [], fn() => (
            !$this->isPublic() && !in_array($this->getName(), $this->getInternals())
        ));
    }

    /** @override */
    public function isPromoted(): bool
    {
        return $this->callOverridingMethod('isPromoted', [], false);
    }

    /** @override */
    public function isStatic(): bool
    {
        return $this->callOverridingMethod('isStatic', [], false);
    }

    /** @override */
    public function isReadOnly(): bool
    {
        return $this->callOverridingMethod('isReadOnly', [], fn() => !!$this->resolveReadOnly());
    }

    /**
     * Checker for nullable case.
     *
     * @return bool
     */
    public function isNullable(): bool
    {
        return (bool) $this->getType()?->isNullable();
    }

    /**
     * Checker for dynamic case.
     *
     * @return bool
     */
    public function isDynamic(): bool
    {
        $name = $this->reference->name;
        $class = $this->reference->class;

        if (!is_object($class) || !property_exists($class, $name)) {
            return false;
        }

        return !in_array($name, array_keys(
            get_class_vars(get_class_name($class))
        ));

        // @cancel
        // Original reflection class needed here.
        // $ref = new ReflectionClass($this->reference->owner->name);
        // if ($ref->hasProperty($name)) {
        //     return false;
        // }
        // return !in_array($name, $this->getDomInternals());
    }

    /** @override */
    public function isInternal(): bool
    {
        if (!$this->reference->owner->isInternal()
            && !$this->isDomClass($this->reference->owner->name)) {
            return false;
        }

        $name = $this->reference->name;

        return in_array($name, $this->getInternals())
            || in_array($name, $this->getDomInternals());
    }

    /**
     * Checker for internal stuff.
     *
     * @return bool
     */
    public function isInternalStuff(): bool
    {
        $name = $this->reference->name;
        $class = $this->reference->class;

        return in_array($name, $this->getInternals($class))
            || in_array($name, $this->getDomInternals($class));
    }

    /**
     * Value peeker/picker with "found" status.
     */
    private function peekValue(object $object = null, string $name = null, bool $default = false): array
    {
        [$value, $found] = [null, false];
        $name ??= $this->reference->name;
        $object ??= $this->reference->class;

        if (!$default && !is_object($object)) {
            return [$value, $found];
        }

        // Dynamics properties.
        if (!$default && is_object($object) && isset($object->$name)) {
            $value = $object->$name;
        }
        // Special weirdos.
        elseif ($this->isInternal()) {
            if (is_numeric($name)
                && is_class_of($object, 'SplFixedArray')) {
                $value = $default ? null : $object[$name];
            }
            elseif ($name == 'storage'
                && is_class_of($object, 'ArrayObject', 'ArrayIterator')) {
                $value = $default ? [] : $object->getArrayCopy();
            }
            elseif ($name == 'storage'
                && is_class_of($object, 'SplObjectStorage')) {
                $value = $default ? [] : $object->__debugInfo()["\0SplObjectStorage\0storage"];
                $found = true;
            }
            elseif (is_equal_of($name, 'flags', 'dllist')
                && is_class_of($object, 'SplDoublyLinkedList')) {
                // SplQueue & SplStack extend SplDoublyLinkedList.
                $value = $default ? ['flags' => 0, 'dllist' => []][$name]
                    : $object->__debugInfo()["\0SplDoublyLinkedList\0{$name}"];
                $found = true;
            }
            elseif (is_equal_of($name, 'flags', 'heap', 'isCorrupted')
                && is_class_of($object, 'SplHeap', 'SplPriorityQueue')) {
                // SplHeap & SplPriorityQueue use same properties.
                $key = is_class_of($object, 'SplHeap') ? 'SplHeap' : 'SplPriorityQueue';
                $value = $default ? ['flags' => 0, 'heap' => [], 'isCorrupted' => false][$name]
                    : $object->__debugInfo()["\0{$key}\0{$name}"];
                $found = true;
            }
            // DOMDocument defaults.
            elseif ($default && is_class_of($object, 'DOMDocument')) {
                $value = ['preserveWhiteSpace' => true, 'strictErrorChecking' => true, 'validateOnParse' => false][$name] ?? null;
                $found = isset($value);
            }
            // Date/Time stuff.
            elseif (!$default && is_class_of($object, 'DateTime', 'DateTimeImmutable', 'DateTimeZone')) {
                // Sorry...
                $export = var_export($object, true); $exportArray = [];
                eval('$exportArray = ['. grep($export, '~array\((.+?)\)~s') .'];');
                if (isset($exportArray[$name])) {
                    $value = $exportArray[$name];
                    $found = true;
                }
            }
        }

        return [$value, $found];
    }

    /**
     * Internal checker.
     */
    private function inInternals(string|object $class, string $name): bool
    {
        return in_array($name, $this->getInternals($class))
            || in_array($name, $this->getDomInternals($class));
    }

    /**
     * Get some internals stuff.
     */
    private function getInternals(string|object $class = null): array
    {
        $ret = [];
        $class = get_class_name($class ?? $this->getClass());

        // Strict check, cos' all Array/SPL stuff are private.
        if (is_equal_of($class, 'ArrayObject', 'ArrayIterator', 'SplObjectStorage')) {
            $ret = ['storage'];
        } elseif (is_equal_of($class, 'SplMaxHeap', 'SplMinHeap', 'SplPriorityQueue')) {
            $ret = ['flags', 'isCorrupted', 'heap'];
        } elseif (is_equal_of($class, 'SplDoublyLinkedList', 'SplStack', 'SplQueue')) {
            $ret = ['flags', 'dllist'];
        }
        // Date/Time stuff.
        elseif (is_class_of($class, 'DateTime', 'DateTimeImmutable')) {
            $ret = ['date', 'timezone', 'timezone_type'];
        } elseif (is_class_of($class, 'DateTimeZone')) {
            $ret = ['timezone', 'timezone_type'];
        }

        return $ret;
    }

    /**
     * Get bloody DOM internal stuff.
     */
    private function getDomInternals(string|object $class = null): array
    {
        $ret = [];
        $class = get_class_name($class ?? $this->getClass());

        // Permissive check, cos' all DOM stuff are public.
        if (is_class_of($class, 'DOMNode')) {
            $tmp1 = ['nodeName', 'nodeValue', 'nodeType', 'parentNode', 'childNodes', 'firstChild',
                'lastChild', 'previousSibling', 'nextSibling', 'attributes', 'ownerDocument', 'namespaceURI',
                'prefix', 'localName', 'baseURI', 'textContent'];
            // Extenders.
            switch (true) {
                case is_class_of($class, 'DOMDocument'):
                    array_prepend($tmp1, 'actualEncoding', 'config', 'doctype', 'documentElement', 'documentURI',
                        'encoding', 'formatOutput', 'implementation', 'preserveWhiteSpace', 'recover', 'resolveExternals',
                        'standalone', 'strictErrorChecking', 'substituteEntities', 'validateOnParse', 'version', 'xmlEncoding',
                        'xmlStandalone', 'xmlVersion', 'firstElementChild', 'lastElementChild', 'childElementCount');
                    break;
                case is_class_of($class, 'DOMElement'):
                    array_prepend($tmp1, 'schemaTypeInfo', 'tagName');
                    break;
                case is_class_of($class, 'DOMAttr'):
                    array_prepend($tmp1, 'name', 'ownerElement', 'schemaTypeInfo', 'specified', 'value');
                    break;
                case is_class_of($class, 'DOMProcessingInstruction'):
                    array_prepend($tmp1, 'target', 'data');
                    break;
                // Mutual property holders (DOMCharacterData > DOMText, DOMComment, DOMCDataSection).
                case is_class_of($class, 'DOMCharacterData'):
                    array_prepend($tmp1, 'data', 'length');
                    if (is_class_of($class, 'DOMText')) {
                        array_prepend($tmp1, 'wholeText');
                    }
                    break;
                // Mutual property holders (DOMNotation, DOMEntity, DOMDocumentType).
                case is_class_of($class, 'DOMNotation', 'DOMEntity', 'DOMDocumentType'):
                    $tmp2 = ['publicId', 'systemId'];
                    if (is_class_of($class, 'DOMEntity')) {
                        array_append($tmp2, 'notationName', 'actualEncoding', 'encoding', 'version');
                    } elseif (is_class_of($class, 'DOMDocumentType')) {
                        array_append($tmp2, 'name', 'entities', 'notations', 'internalSubset');
                    }
                    $tmp1 = array_merge($tmp2, $tmp1);
                    break;
            }
            $ret = array_merge($ret, $tmp1);
        } elseif (is_class_of($class, 'DOMNodeList', 'DOMNamedNodeMap')) {
            $ret = ['length'];
        } elseif (is_class_of($class, 'DOMXPath')) {
            $ret = ['document'];
        }

        return $ret;
    }

    /**
     * Get some more internals stuff.
     */
    private function getInternalStuff(string $for): array|bool|null
    {
        $name = $this->getName();
        $class = $this->getClass();

        $search = fn($nam) => strpfx($nam, $name);
        if ($for == 'type') { // For type/nullable stuff.
            $return = fn($found, $type) => [$type, strsrc($found, '?')];
        } elseif ($for == 'readonly') { // For readonly stuff.
            $return = fn($found) => strsrc($found, '!');
        }

        $types = [];
        // Strict check, cos' all Array/SPL stuff are private.
        if (is_equal_of($class, 'ArrayObject', 'ArrayIterator', 'SplObjectStorage')) {
            $types[] = ['array' => ['storage!']];
        } elseif (is_equal_of($class, 'SplMaxHeap', 'SplMinHeap', 'SplPriorityQueue')) {
            $types[] = ['int' => ['flags!'], 'bool' => ['isCorrupted!'], 'array' => ['heap!']];
        } elseif (is_equal_of($class, 'SplDoublyLinkedList', 'SplStack', 'SplQueue')) {
            $types[] = ['int' => ['flags!'], 'array' => ['dllist!']];
        }
        // Permissive check, cos' all DOM stuff are public.
        elseif (is_class_of($class, 'DOMNode')) {
            $types[] = ['string' => ['nodeName!', 'nodeValue?', 'namespaceURI!?', 'prefix', 'localName!?', 'baseURI!?', 'textContent'], 'int' => ['nodeType!'], 'DOMNode' => ['parentNode!?', 'firstChild!?', 'lastChild!?', 'previousSibling!?', 'nextSibling!?'], 'DOMNodeList' => ['childNodes!'], 'DOMNamedNodeMap' => ['attributes!?'], 'DOMDocument' => ['ownerDocument!?']];
            // Extenders.
            switch (true) {
                case is_class_of($class, 'DOMDocument'):
                    $types[] = ['string' => ['actualEncoding!', 'documentURI?', 'encoding', 'version', 'xmlEncoding!?', 'xmlVersion'], 'DOMConfiguration' => ['config!'], 'DOMDocumentType' => ['doctype!'], 'DOMElement' => ['documentElement!?'], 'bool' => ['formatOutput', 'preserveWhiteSpace', 'recover', 'resolveExternals', 'standalone', 'strictErrorChecking', 'substituteEntities', 'validateOnParse', 'xmlStandalone'], 'DOMImplementation' => ['implementation!']];
                    break;
                case is_class_of($class, 'DOMElement'):
                    $types[] = ['bool' => ['schemaTypeInfo!'], 'string' => ['tagName!']];
                    break;
                case is_class_of($class, 'DOMAttr'):
                    $types[] = ['string' => ['name!', 'value'], 'DOMElement' => ['ownerElement!'], 'bool' => ['schemaTypeInfo!', 'specified!']];
                    break;
                case is_class_of($class, 'DOMProcessingInstruction'):
                    $types[] = ['string' => ['target!', 'data']];
                    break;
                // Mutual property holders (DOMCharacterData > DOMText, DOMComment, DOMCDataSection).
                case is_class_of($class, 'DOMCharacterData'):
                    $types[] = ['string' => ['data'], 'int' => ['length!']];
                    if (is_class_of($class, 'DOMText')) {
                        $types[] = ['string' => ['wholeText!']];
                    }
                    break;
                // Mutual property holders (DOMNotation, DOMEntity, DOMDocumentType).
                case is_class_of($class, 'DOMNotation', 'DOMEntity', 'DOMDocumentType'):
                    $types[] = ['string' => ['publicId!', 'systemId!']];
                    if (is_class_of($class, 'DOMEntity')) {
                        $types[] = ['string' => ['notationName!', 'actualEncoding', 'encoding!', 'version!']];
                    } elseif (is_class_of($class, 'DOMDocumentType')) {
                        $types[] = ['string' => ['name!', 'internalSubset!'], 'DOMNamedNodeMap' => ['entities!', 'notations!']];
                    }
                    break;
            }
        } elseif (is_class_of($class, 'DOMNodeList', 'DOMNamedNodeMap')) {
            $types[] = ['int' => ['length!']];
        } elseif (is_class_of($class, 'DOMXPath')) {
            $types[] = ['DOMDocument' => ['document']];
        }
        // Date/Time stuff.
        elseif (is_class_of($class, 'DateTime', 'DateTimeImmutable')) {
            $types[] = ['string' => ['date!', 'timezone!'], 'int' => ['timezone_type!']];
        } elseif (is_class_of($class, 'DateTimeZone')) {
            $types[] = ['string' => ['timezone!'], 'int' => ['timezone_type!']];
        }

        // Search in types.
        foreach($types as $type) {
            foreach($type as $type => $names) {
                $found = array_find($names, $search);
                if ($found) {
                    return $return($found, $type);
                }
            }
        }

        return null;
    }

    /**
     * Try to resolve type.
     */
    private function resolveType(): array|null
    {
        if (!$this->isDynamic()) {
            if ($this->isInternal()) {
                return $this->getInternalStuff('type');
            }

            if ($type = parent::getType()) {
                return [
                    ($type instanceof ReflectionNamedType) ? $type->getName() : (string) $type,
                    $type->allowsNull()
                ];
            }
        }

        return null;
    }

    /**
     * Try to resolve read-only state.
     */
    private function resolveReadOnly(): bool|null
    {
        if (!$this->isDynamic()) {
            if ($this->isInternal()) {
                return $this->getInternalStuff('readonly');
            }
        }

        return null;
    }

    /**
     * Try to call parent method, or use fallback.
     */
    private function callOverridingMethod(string $method, array $methodArgs, mixed $fallback, array $fallbackArgs = []): mixed
    {
        // Handle: "Internal error: Failed to retrieve the default value ..." error.
        try {
            return parent::$method(...$methodArgs);
        } catch (Throwable) {
            return ($fallback instanceof Closure) ? $fallback(...$fallbackArgs) : $fallback;
        }
    }

    /**
     * DOM class checker.
     */
    private function isDomClass(string|object $class = null): bool
    {
        $class ??= $this->reference->owner->name;

        return is_class_of($class, 'DOMNode', 'DOMNodeList', 'DOMNamedNodeMap', 'DOMXPath');
    }
}

/*** Callable stuff. ***/

/**
 * An extended ReflectionMethod class.
 *
 * @package froq\util
 * @object  ReflectionMethodExtended
 * @author  Kerem Güneş
 * @since   5.27
 */
class ReflectionMethodExtended extends ReflectionMethod
{
    use ReflectionCallableTrait;
}

/**
 * An extended ReflectionFunction class.
 *
 * @package froq\util
 * @object  ReflectionFunctionExtended
 * @author  Kerem Güneş
 * @since   5.27
 */
class ReflectionFunctionExtended extends ReflectionFunction
{
    use ReflectionCallableTrait;
}

/**
 * A reflection class that combines ReflectionMethod & ReflectionFunction as one class
 * and adds some other utility methods.
 *
 * @package froq\util
 * @object  ReflectionCallable
 * @author  Kerem Güneş
 * @since   5.27
 */
class ReflectionCallable implements Reflector
{
    use ReflectionCallableTrait;

    /**
     * Proxy for reflection object properties.
     *
     * @param  string $property
     * @return string
     * @throws Error
     * @magic
     */
    public function __get(string $property): string
    {
        // For name, class actually.
        if (property_exists($this->reference->reflection, $property)) {
            return $this->reference->reflection->$property;
        }

        throw new Error(sprintf(
            'Undefined property %s::$%s / %s::$%s',
            $this::class, $property, $this->reference->reflection::class, $property
        ));
    }

    /**
     * Proxy for reflection object methods.
     *
     * @param  string $method
     * @param  array  $methodArgs
     * @return mixed
     * @throws Error
     * @magic
     */
    public function __call(string $method, array $methodArgs): mixed
    {
        // For all parent methods actually.
        if (method_exists($this->reference->reflection, $method)) {
            return $this->reference->reflection->$method(...$methodArgs);
        }

        throw new Error(sprintf(
            'Undefined method %s::%s() / %s::%s()',
            $this::class, $method, $this->reference->reflection::class, $method
        ));
    }

    /** @magic */
    public function __toString(): string
    {
        return $this->reference->reflection->__toString();
    }

    /**
     * Check whether reflection object is a method.
     *
     * @return bool
     */
    public function isMethod(): bool
    {
        return ($this->reference->reflection instanceof ReflectionMethod);
    }

    /**
     * Check whether reflection object is a function.
     *
     * @return bool
     */
    public function isFunction(): bool
    {
        return ($this->reference->reflection instanceof ReflectionFunction);
    }
}

/**
 * A trait that used by ReflectionCallable, ReflectionMethodExtended & ReflectionFunctionExtended classes.
 *
 * @package froq\util
 * @object  ReflectionCallableTrait
 * @author  Kerem Güneş
 * @since   5.27
 */
trait ReflectionCallableTrait
{
    /** Method/function */
    private object $reference;

    /**
     * Constructor.
     *
     * @param  string|callable|array|object $callable
     * @param  string|null                  $name
     * @causes ReflectionException
     */
    public function __construct(string|callable|array|object $callable, string $name = null)
    {
        // When "Foo.bar" or "Foo::bar" given.
        if (is_string($callable) && preg_match('~(.+)(?:\.|::)(\w+)~', $callable, $match)) {
            $callable = array_slice($match, 1);
        } elseif ($name !== null && (is_string($callable) || is_object($callable))) {
            $callable = [$callable, $name];
        }

        $this->reference = qo(
            callable: $callable,
            reflection: is_array($callable) ? new ReflectionMethod(...$callable)
                : new ReflectionFunction($callable),
        );

        // Call super constructor.
        if (!$this instanceof ReflectionCallable) {
            is_array($callable) ? parent::__construct(...$callable)
                : parent::__construct($callable);
        }
    }

    /** @magic */
    public function __debugInfo(): array
    {
        return ($this->reference->reflection instanceof ReflectionMethod)
             ? ['name' => $this->reference->reflection->name, 'class' => $this->reference->reflection->class]
             : ['name' => $this->reference->reflection->name];
    }

    /**
     * Get class.
     *
     * @return string
     * @throws ReflectionException
     */
    public function getClass(): string
    {
        try { // For non-exist ReflectionFunction stuff.
            return @ $this->reference->reflection->class;
        } catch (Throwable) {
            throw new ReflectionException(sprintf(
                'Invalid call as %s::getClass()', $this::class
            ));
        }
    }

    /**
     * Get declaring class.
     *
     * @return ReflectionClassExtended
     * @throws ReflectionException
     */
    public function getDeclaringClass(): ReflectionClassExtended
    {
        try { // For non-exist ReflectionFunction stuff.
            return new ReflectionClassExtended($this->reference->reflection->getDeclaringClass()->name);
        } catch (Throwable) {
            throw new ReflectionException(sprintf(
                'Invalid call as %s::getDeclaringClass()', $this::class
            ));
        }
    }

    /**
     * Set of interfaces.
     *
     * @return Set
     */
    public function interfaces(): Set
    {
        return Set::from($this->getDeclaringClass()->getInterfaces())
            ->filter(fn($ref) => (
                $ref->hasMethod($this->name) &&
                $ref->getMethod($this->name)->class == $ref->name
            ));
    }

    /**
     * Get interfaces.
     *
     * @return array<ReflectionInterface|null>
     */
    public function getInterfaces(): array
    {
        return $this->interfaces()->toArray();
    }

    /**
     * Get interface.
     *
     * @param  string|null $name
     * @return ReflectionInterface|null
     */
    public function getInterface(string $name = null): ReflectionInterface|null
    {
        return ($name == null) ? $this->interfaces()->last()
             : $this->interfaces()->find(fn($ref) => $ref->name == $name);
    }

    /**
     * Get interface names.
     *
     * @return array<string|null>
     */
    public function getInterfaceNames(): array
    {
        return $this->interfaces()->map(fn($ref) => $ref->name)->toArray();
    }

    /**
     * Set of traits.
     *
     * @return Set
     */
    public function traits(): Set
    {
        return Set::from($this->getDeclaringClass()->getTraits())
            ->filter(fn($ref) => (
                $ref->hasMethod($this->name) &&
                $ref->getMethod($this->name)->class == $ref->name
            ));
    }

    /**
     * Get traits.
     *
     * @return array<ReflectionTrait|null>
     */
    public function getTraits(): array
    {
        return $this->traits()->toArray();
    }

    /**
     * Get trait.
     *
     * @param  string|null $name
     * @return ReflectionTrait|null
     */
    public function getTrait(string $name = null): ReflectionTrait|null
    {
        return ($name == null) ? $this->traits()->last()
             : $this->traits()->find(fn($ref) => $ref->name == $name);
    }

    /**
     * Get trait names.
     *
     * @return array<string|null>
     */
    public function getTraitNames(): array
    {
        return $this->traits()->map(fn($ref) => $ref->name)->toArray();
    }

    /**
     * Map of attributes.
     *
     * @return Map<ReflectionAttribute>
     */
    public function attributes(): Map
    {
        return Map::from($this->getAttributes());
    }

    /**
     * Has attribute.
     *
     * @param  string $name
     * @return bool
     */
    public function hasAttribute(string $name): bool
    {
        return $this->getAttribute($name) != null;
    }

    /**
     * Get attribute.
     *
     * @param  string $name
     * @return ReflectionAttribute|null
     */
    public function getAttribute(string $name): ReflectionAttribute|null
    {
        return $this->attributes()->find(fn($ref) => $ref->getName() == $name);
    }

    /**
     * Get attribute names.
     *
     * @return array
     */
    public function getAttributeNames(): array
    {
        return $this->attributes()->map(fn($ref) => $ref->getName())->toArray();
    }

    /**
     * Get visibility.
     *
     * @return string
     */
    public function getVisibility(): string
    {
        return $this->reference->reflection->isPublic() ? 'public'
             : ($this->reference->reflection->isPrivate() ? 'private' : 'protected');
    }

    /**
     * Get modifier names.
     *
     * @return array
     */
    public function getModifierNames(): array
    {
        return Reflection::getModifierNames($this->getModifiers());
    }

    /** @override */
    public function getReturnType(): ReflectionTypeExtended|null
    {
        if ($type = $this->reference->reflection->getReturnType()) {
            return new ReflectionTypeExtended(
                ($type instanceof ReflectionNamedType) ? $type->getName() : (string) $type,
                $type->allowsNull()
            );
        }
        return null;
    }

    /**
     * Set of parameters.
     *
     * @return Set<ReflectionParameterExtended>
     */
    public function parameters(): Set
    {
        return new Set($this->collectParameters(extend: true));
    }

    /** @override */
    public function hasParameter(string|int $name): bool
    {
        if (is_int($name)) {
            return isset($this->reference->reflection->getParameters()[$name]);
        }

        return $this->collectParameters(name: $name)
            ->has($name);
    }

    /** @override */
    public function getParameter(string|int $name): ReflectionParameterExtended|null
    {
        if (is_int($name)) {
            $parameter = $this->reference->reflection->getParameters()[$name] ?? null;
            return $parameter ? new ReflectionParameterExtended($this->reference->callable, $name) : null;
        }

        return $this->collectParameters(name: $name, extend: true)
            ->get($name);
    }

    /** @override */
    public function getParameters(): array
    {
        return $this->collectParameters(extend: true)
            ->values();
    }

    /** @alias getNumberOfParameters() */
    public function getParametersCount(): int
    {
        return $this->getNumberOfParameters();
    }

    /**
     * Get parameter names.
     *
     * @return array
     */
    public function getParameterNames(): array
    {
        return $this->collectParameters()
            ->keys();
    }

    /**
     * Get parameter (default) values.
     *
     * @param  bool $assoc
     * @return array
     */
    public function getParameterValues(bool $assoc = false): array
    {
        $map = $this->collectParameters(extend: true, fill: true);
        return $assoc ? $map->toArray() : $map->values();
    }

    /**
     * Collect parameters, extend if requested.
     */
    private function collectParameters(bool $fill = false, bool $extend = false, string $name = null): Map
    {
        $map = new Map();

        foreach ($this->reference->reflection->getParameters() as $parameter) {
            // When single one wanted.
            if ($name && $name != $parameter->name) {
                continue;
            }
            $map[$parameter->name] = $parameter->name;
        }

        // Extend with instances & values optionally.
        $extend && ($map = $this->extendParameters($map, $fill));

        return $map;
    }

    /**
     * Extend parameters, add default values if requested.
     */
    private function extendParameters(Map $map, bool $fill = false): Map
    {
        foreach ($map->keys() as $name) {
            $parameter = new ReflectionParameterExtended($this->reference->callable, $name);

            // Fill with default value.
            $map[$name] = $fill ? $parameter->getDefaultValue() : $parameter;
        }

        return $map;
    }
}

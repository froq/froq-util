<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */

/**
 * Since these classes create & return (their) instances inside,
 * typed properties become problematic as these types conflict.
 * So here, we use class_alias() instead of extending bases.
 * Eg: XReflectionType::of() => froq\reflection\ReflectionType
 * @see Type#reflection & Type#reflect()
 */

// Extensions.
// class XReflection extends froq\reflection\Reflection {}
// class XReflectionObject extends froq\reflection\ReflectionObject {}
// class XReflectionClass extends froq\reflection\ReflectionClass {}
// class XReflectionClassConstant extends froq\reflection\ReflectionClassConstant {}
// class XReflectionProperty extends froq\reflection\ReflectionProperty {}
// class XReflectionMethod extends froq\reflection\ReflectionMethod {}
// class XReflectionFunction extends froq\reflection\ReflectionFunction {}
// class XReflectionParameter extends froq\reflection\ReflectionParameter {}
// class XReflectionType extends froq\reflection\ReflectionType {}
// class XReflectionAttribute extends froq\reflection\ReflectionAttribute {}
class_alias(froq\reflection\Reflection::class, XReflection::class);
class_alias(froq\reflection\ReflectionObject::class, XReflectionObject::class);
class_alias(froq\reflection\ReflectionClass::class, XReflectionClass::class);
class_alias(froq\reflection\ReflectionClassConstant::class, XReflectionClassConstant::class);
class_alias(froq\reflection\ReflectionProperty::class, XReflectionProperty::class);
class_alias(froq\reflection\ReflectionMethod::class, XReflectionMethod::class);
class_alias(froq\reflection\ReflectionFunction::class, XReflectionFunction::class);
class_alias(froq\reflection\ReflectionParameter::class, XReflectionParameter::class);
class_alias(froq\reflection\ReflectionType::class, XReflectionType::class);
class_alias(froq\reflection\ReflectionAttribute::class, XReflectionAttribute::class);

// Additions.
// class ReflectionTrait extends froq\reflection\ReflectionTrait {}
// class ReflectionInterface extends froq\reflection\ReflectionInterface {}
// class ReflectionNamespace extends froq\reflection\ReflectionNamespace {}
// class ReflectionCallable extends froq\reflection\ReflectionCallable {}
class_alias(froq\reflection\ReflectionTrait::class, ReflectionTrait::class);
class_alias(froq\reflection\ReflectionInterface::class, ReflectionInterface::class);
class_alias(froq\reflection\ReflectionNamespace::class, ReflectionNamespace::class);
class_alias(froq\reflection\ReflectionCallable::class, ReflectionCallable::class);
class_alias(froq\reflection\ReflectionClosure::class, ReflectionClosure::class);

// Additions aliases.
// class XReflectionTrait extends froq\reflection\ReflectionTrait {}
// class XReflectionInterface extends froq\reflection\ReflectionInterface {}
// class XReflectionNamespace extends froq\reflection\ReflectionNamespace {}
// class XReflectionCallable extends froq\reflection\ReflectionCallable {}
class_alias(froq\reflection\ReflectionTrait::class, XReflectionTrait::class);
class_alias(froq\reflection\ReflectionInterface::class, XReflectionInterface::class);
class_alias(froq\reflection\ReflectionNamespace::class, XReflectionNamespace::class);
class_alias(froq\reflection\ReflectionCallable::class, XReflectionCallable::class);
class_alias(froq\reflection\ReflectionClosure::class, XReflectionClosure::class);

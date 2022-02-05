<?php

namespace OnPHPToolkit;

use OnPHP\Core\Base\Assert;
use OnPHP\Main\Util\ClassUtils;

class ClosureResolver
{
	const METHOD_SEPARATOR = '.';
	const STATIC_METHOD_SEPARATOR = '::';

	public static function resolveObject($object, $property)
	{
		if (is_string($object)) {
			Assert::classExists($object);
			$object = new $object;
		}

		Assert::isObject($object);

		$properties = explode(self::METHOD_SEPARATOR, $property);

		foreach ($properties as $i => $property) {
			if ($i < count($properties) - 1) {
				$object = self::guessObject($object, $property);
			} else {
				return $object;
			}
		}
	}

	public static function resolveMethod($object, $property, $args = array())
	{
		if (is_string($object)) {
			Assert::classExists($object);
			$object = new $object;
		}

		Assert::isObject($object);

		$properties = explode(self::METHOD_SEPARATOR, $property);

		foreach ($properties as $i => $property) {
			if ($i < count($properties) - 1) {
				$object = self::guessObject($object, $property);
			} else {
				Assert::isNotNull($object, 'Delegate subject is null');

				$method = self::guessMethod($object, $property);
				return Delegate::create(array($object, $method), $args);
			}
		}
	}

	public static function resolveStaticMethod($methodSignature, $args = array())
	{
		ClassUtils::checkStaticMethod($methodSignature);
		return Delegate::create($methodSignature, $args);
	}

	public static function isDelegateMethod($methodName)
	{
		return (bool)(
			strstr($methodName, self::METHOD_SEPARATOR)
			|| strstr($methodName, self::STATIC_METHOD_SEPARATOR)
		);
	}

	private static function guessMethod($object, $property)
	{
		if ($object instanceof Prototyped
			&& $object->proto()->isPropertyExists($property)
		) {
			$object->proto()->getPropertyByName($property)->getGetter();
		} elseif($object instanceof Date) {
			return $property;
		}

		if (method_exists($object, $property)) {
			return $property;
		}

		if (substr($property, 0, 3) == 'get') {
			return $property;
		}

		return EntityNamingUtils::stringToMethodName($property, 'get');
	}


	private static function guessObject($object, $property)
	{
		if ($object instanceof Identifiable) {
			$getter = self::guessMethod($object, $property);
			$childObject = $object->{$getter}();

			if ($object->getId()
				|| (
					$childObject instanceof Identifiable
					&& $childObject->getId()
				)
			) {
				return $childObject;
			}
		}

		if ($object instanceof Prototyped) {
			$property = $object->proto()->getPropertyByName($property);

			if ($className = $property->getClassName()) {
				return new $className;
			}
		}

		throw new WrongArgumentException("Can't extract object");
	}
}
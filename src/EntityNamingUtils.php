<?php

namespace OnPHPToolkit;

class EntityNamingUtils
{
	/**
	 * Convert snake_case string like "my_class" to UpperCamelCase.
	 *
	 * @param string $string
	 * @return string
	 */
	public static function stringToClassName(string $string): string
	{
		return ucfirst(self::arrayToMethodName(explode('_', $string)));
	}

	/**
	 * Convert snake_case string like "my_property" to camelCase myProperty.
	 * @param string $string
	 * @return string
	 */
	public static function stringToPropertyName(string $string): string
	{
		return self::arrayToMethodName(explode('_', $string));
	}

	/**
	 * Convert snake_case string like "my_method" to camelCase.
	 * And also could add $prefix and/or $postfix. If prefix or
	 * postfix defined, final string will be like prefixMyMethodPostfix
	 *
	 * @param string $string
	 * @param string|null $prefix
	 * @param string|null $postfix
	 * @return string
	 */
	public static function stringToMethodName(
		string $string,
		string $prefix = null,
		string $postfix = null
	): string
	{
		return self::arrayToMethodName(explode('_', $string), $prefix, $postfix);
	}

	/**
	 * Convert array like array('my', 'method') to camelCase myMethod.
	 * And also could add $prefix and/or $postfix. If prefix or
	 * postfix defined, final string will be like prefixMyMethodPostfix
	 *
	 * @param unknown_type $string
	 */
	public static function arrayToMethodName($array, $prefix = null, $postfix = null)
	{
		foreach ($array as $i => &$str) {
			if ($i != 0
				|| ($i == 0 && !empty($prefix))
			) {
				$str = ucfirst($str);
			}
		}

		$res = implode("", $array);

		if($prefix !== null) {
			$res = $prefix.$res;
		}

		if($postfix !== null) {
			$res .= ucfirst($postfix);
		}

		return lcfirst($res);
	}

}
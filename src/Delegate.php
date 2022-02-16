<?php

namespace sugrob\OnPHP\Toolkit;

use OnPHP\Core\Base\Assert;

class Delegate
{
	const METHOD_SEPARATOR = '.';
	const STATIC_METHOD_SEPARATOR = '::';

	/**
	 * @var Class or Object
	 */
	protected $chain;

	protected $args = array();

	/**
	 * @param type $target
	 * @param array $args
	 * @return Delegate
	 */
	public static function create($target, array $args = array())
	{
		return new self($target, $args);
	}

	/**
	 * I am understand callback function like "Foo::bar" or "Foo.bar"
	 * or array("Foo", "bar") or just "foo"
	 * or array(Object, method)
	 *
	 * @param mixed $target Array or string
	 * @param array $args
	 */
	public function __construct($target, array $args = array())
	{
		if (is_array($target)) {
			Assert::isEqual(
				count($target),
				2,
				'First parameter must be an array with exactly two members'
			);

			$this->chain = $target;
		} elseif (is_string($target)) {
			if (strstr($target, self::STATIC_METHOD_SEPARATOR)) {
				$this->chain = explode(self::STATIC_METHOD_SEPARATOR, $target);
			} elseif (strstr($target, self::METHOD_SEPARATOR)) {
				$this->chain = explode(self::METHOD_SEPARATOR, $target);
			} else {
				$this->chain[] = $target;
			}
		}

		if (count($this->chain) == 1) {
			Assert::isTrue(function_exists($this->chain[0]), 'Function name is empty');
		} elseif (count($this->chain) == 2) {
			if (!is_object($this->chain[0])) {
				Assert::classExists($this->chain[0]);
			}

			Assert::methodExists($this->chain[0], $this->chain[1]);
		}

		$this->args = $args;
	}

	/*
	 * Executes each closure
	 */
	public function run(...$custom_args)
	{
		return call_user_func_array(
			$this->chain,
			$custom_args ? $custom_args : $this->args
		);
	}

	public function toString()
	{
		return implode(self::METHOD_SEPARATOR, $this->chain);
	}
}
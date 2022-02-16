<?php

namespace sugrob\OnPHP\Toolkit\Utils;

interface IHasher {
	/**
	 * @param $string
	 * @return string
	 */
	public function hash(string $string):string;

	/**
	 * @param string $string
	 * @param string $hash
	 * @return bool
	 */
	public function compare(string $string, string $hash):bool;
}
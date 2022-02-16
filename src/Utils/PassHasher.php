<?php

namespace sugrob\OnPHP\Toolkit\Utils;

class PassHasher implements IHasher
{
	const DEFAULT_HASH_POWER = 256; //2^8

	private $salt;
	private $power;

	public function __construct($salt, $pow = self::DEFAULT_HASH_POWER) {
		$this->salt = $salt;
		$this->power = $pow;
	}

	public function hash(string $string):string {
		return $this->multiHash($string.$this->salt);
	}

	public function compare(string $string, string $hash):bool {
		return $this->hash($string) == $hash;
	}

	private function multiHash(string $string):string {
		for ($i = 0; $i < $this->power; $i++) {
			$string = md5($string);
		}

		return $string;
	}
}
<?php

namespace sugrob\OnPHP\Toolkit\Flow;

use OnPHP\Main\Flow\Model;
use OnPHP\Main\Util\ArrayUtils;

class CumulativeModel extends \OnPHP\Main\Flow\Model
{
	/**
	 * @return CumulativeModel
	 **/
	public static function create()
	{
		return new self;
	}

	/**
	 * @param Model $model
	 * @param bool $overwrite
	 * @return CumulativeModel
	 */
	public function merge(Model $model, $overwrite = false)
	{
		if ($externalVars = $model->getList()) {
			$originalVars = $this->getList();
			$result = ArrayUtils::mergeRecursiveUnique($originalVars, $externalVars, $overwrite);
			$this->clean();

			foreach ($result as $key => $value) {
				$this->set($key, $value);
			}
		}

		return $this;
	}
}
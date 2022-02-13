<?php

namespace sugrob\OnPHPToolkit\Flow;

use OnPHP\Main\Flow\Model;

class ConsistentModel extends Model
{
	protected $errors = array();

	/**
	 * @return ConsistentModel
	 **/
	public static function create()
	{
		return new self;
	}

	public function isSuccessful()
	{
		return empty($this->errors);
	}

	public function getErrors()
	{
		return $this->errors;
	}

	/**
	 * @param array $errors
	 * @return ConsistentModel
	 */
	public function setErrors(array $errors)
	{
		$this->errors = $errors;

		return $this;
	}

	/**
	 * @param $key
	 * @param $error
	 * @return ConsistentModel
	 */
	public function addError($key, $error)
	{
		$this->errors[$key] = $error;

		return $this;
	}

	/**
	 * @param $key
	 * @return ConsistentModel
	 */
	public function dropError($key)
	{
		if (isset($this->errors[$key])) {
			unset($this->errors[$key]);
		}

		return $this;
	}

	/**
	 * @param $key
	 * @return bool
	 */
	public function hasError($key)
	{
		return isset($this->errors[$key]);
	}

	/**
	 * @return ConsistentModel
	 */
	public function clearErrors()
	{
		$this->errors = array();

		return $this;
	}

	/**
	 * @param Model $model
	 * @param bool $overwrite
	 * @return ConsistentModel
	 */
	public function merge(Model $model, $overwrite = false)
	{
		parent::merge($model, $overwrite);

		if ($model instanceof ConsistentModel
			&& !$model->isSuccessful()
		) {
			foreach ($model->getErrors() as $key => $error) {
				if (!$overwrite && $this->hasError($key))
					continue;

				$this->addError($key, $error);
			}
		}

		return $this;
	}
}
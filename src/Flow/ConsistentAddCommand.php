<?php

namespace sugrob\OnPHPToolkit\Flow;

use OnPHP\Core\Base\Identifiable;
use OnPHP\Core\Base\Prototyped;
use OnPHP\Core\Form\Form;
use OnPHP\Core\Form\FormUtils;
use OnPHP\Main\Flow\HttpRequest;
use OnPHP\Main\Flow\ModelAndView;
use OnPHP\Main\Util\ClassUtils;

class ConsistentAddCommand extends ConsistentTakeCommand
{
	/**
	 * @return ConsistentAddCommand
	 **/
	public static function create(): ConsistentAddCommand
	{
		return new self;
	}

	public function run(Prototyped $subject, Form $form, HttpRequest $request): ConsistentModel
	{
		$form->markGood('id');

		if (!$form->getErrors()) {
			FormUtils::form2object($form, $subject);

			return parent::run($subject, $form, $request);
		}

		$model = ConsistentModel::create();

		foreach ($form->getErrors() as $name => $error) {
			$model->addError($name, $error);
		}

		return $model;
	}

	protected function daoMethod()
	{
		return 'add';
	}
}
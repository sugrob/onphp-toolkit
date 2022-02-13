<?php

namespace sugrob\OnPHPToolkit\Flow;

use OnPHP\Core\Base\Identifiable;
use OnPHP\Core\Base\Prototyped;
use OnPHP\Core\Form\Form;
use OnPHP\Core\Form\FormUtils;
use OnPHP\Main\Flow\HttpRequest;
use OnPHP\Main\Flow\ModelAndView;
use OnPHP\Main\Util\ClassUtils;

class ConsistentSaveCommand extends ConsistentTakeCommand
{
	/**
	 * @return ConsistentSaveCommand
	 **/
	public static function create(): ConsistentSaveCommand
	{
		return new self;
	}

	public function run(Prototyped $subject, Form $form, HttpRequest $request): ConsistentModel
	{
		if (!$form->getErrors()) {
			ClassUtils::copyProperties($form->getValue('id'), $subject);

			FormUtils::form2object($form, $subject, false);

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
		return 'save';
	}
}
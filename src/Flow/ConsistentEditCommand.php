<?php

namespace OnPHPToolkit\Flow;

use OnPHP\Core\Base\Prototyped;
use OnPHP\Core\Form\Form;
use OnPHP\Core\Form\FormUtils;
use OnPHP\Main\Flow\HttpRequest;
use OnPHP\Main\Flow\ModelAndView;

class ConsistentEditCommand implements ConsistentCommand
{
	/**
	 * @return ConsistentEditCommand
	 */
	public static function create()
	{
		return new self;
	}

	public function run(Prototyped $subject, Form $form, HttpRequest $request): ConsistentModel
	{
		$model = ConsistentModel::create();

		$model->set("form", $form);

		if ($object = $form->getValue('id')) {
			FormUtils::object2form($object, $form);
		}

		return $model;
	}
}
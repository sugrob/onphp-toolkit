<?php

namespace OnPHPToolkit\Flow;

use OnPHP\Core\Base\Identifiable;
use OnPHP\Core\Base\Prototyped;
use OnPHP\Core\Form\Form;
use OnPHP\Main\Flow\HttpRequest;

class ConsistentDropCommand implements ConsistentCommand
{
	const DROP_COMMAND_MISSING_ID = "Drop command: id is missing";

	/**
	 * @return ConsistentDropCommand
	 **/
	public static function create(): ConsistentDropCommand
	{
		return new self;
	}

	public function run(Prototyped $subject, Form $form, HttpRequest $request): ConsistentModel
	{
		$model = ConsistentModel::create();

		if ($object = $form->getValue('id')) {

			if ($object instanceof Identifiable) {

				$object->dao()->drop($object);

			} else {
				// already deleted
				$form->markMissing('id');

				if ($error = $form->getTextualErrorFor("id")) {
					$model->addError("id", $error);
				} else {
					$model->addError("id", self::DROP_COMMAND_MISSING_ID);
				}
			}
		}

		return $model;
	}
}
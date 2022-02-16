<?php

namespace sugrob\OnPHP\Toolkit\Flow;

use OnPHP\Core\Base\Prototyped;
use OnPHP\Core\Form\Form;
use OnPHP\Main\Flow\HttpRequest;

abstract class ConsistentTakeCommand implements ConsistentCommand
{
	abstract protected function daoMethod();

	public function run(Prototyped $subject, Form $form, HttpRequest $request): ConsistentModel
	{
		$subject = $subject->dao()->{$this->daoMethod()}($subject);

		return ConsistentModel::create()->
			set('id', $subject->getId());
	}
}
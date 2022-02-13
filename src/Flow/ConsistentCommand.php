<?php

namespace sugrob\OnPHPToolkit\Flow;

use OnPHP\Core\Base\Prototyped;
use OnPHP\Core\Form\Form;
use OnPHP\Main\Flow\HttpRequest;

/**
 * @ingroup Flow
 **/
interface ConsistentCommand
{
	/**
	 * @param Prototyped $subject
	 * @param Form $form
	 * @param HttpRequest $request
	 * @return ConsistentModel
	 */
	public function run(Prototyped $subject, Form $form, HttpRequest $request): ConsistentModel;
}
<?php

namespace OnPHPToolkit\Flow;

use OnPHP\Core\Base\Assert;
use OnPHP\Core\Base\Prototyped;
use OnPHP\Core\Base\Stringable;
use OnPHP\Core\Exception\ClassNotFoundException;
use OnPHP\Core\Exception\MissingElementException;
use OnPHP\Main\Base\LightMetaProperty;
use OnPHP\Meta\Entity\MetaRelation;

class ClassRelation implements Stringable
{
	/**
	 * @var MetaRelation
	 */
	protected $relation;

	/**
	 * @var string
	 */
	protected $parentClass;

	/**
	 * @var string
	 */
	protected $parentPropery;

	/**
	 * @var string
	 */
	protected $childClass;

	/**
	 * @var string
	 */
	protected $childProperty;


	/**
	 * ClassRelation constructor.
	 * @param string $parentClass
	 * @param string $parentProperty
	 * @param string $childClass
	 * @param string $childProperty
	 * @param int $relationId
	 * @throws ClassNotFoundException
	 */
	public function __construct(
		string $parentClass,
		string $parentProperty,
		string $childClass,
		string $childProperty,
		int $relationId
	)
	{
		Assert::classExists($parentClass);
		Assert::classExists($childClass);

		$this->parentClass = $parentClass;
		$this->parentPropery = $parentProperty;
		$this->childClass = $childClass;
		$this->childProperty = $childProperty;
		$this->relation = MetaRelation::create($relationId);
	}

	/**
	 * @param string $parentClass
	 * @param string $parentProperty
	 * @param string $childClass
	 * @param string $childProperty
	 * @param int $relationId
	 * @return ClassRelation
	 */
	public static function create(
		string $parentClass,
		string $parentProperty,
		string $childClass,
		string $childProperty,
		int $relationId
	)
	{
		return new self($parentClass, $parentProperty, $childClass, $childProperty, $relationId);
	}

	/**
	 * @param Prototyped $subject
	 * @param string $propertyName
	 * @return ClassRelation|null
	 * @throws MissingElementException
	 */
	public static function createFromObject(Prototyped $subject, string $propertyName)
	{
		$property = $subject->proto()->getPropertyByName($propertyName);

		if ($property->getRelationId() == MetaRelation::ONE_TO_MANY) {
			$class = $property->getContainerName(get_class($subject));
			$dao = new $class($subject);

			//Ищем название свойства дочернено объекта
			$map = $dao->getDao()->getProtoClass()->getMapping();

			if (in_array($dao->getParentIdField(), $map)) {

				$childProperty = array_search($dao->getParentIdField(), $map);

				return new self(
					get_class($subject),
					'id',
					$property->getClassName(),
					$childProperty,
					$property->getRelationId()
				);
			} else {
				throw new WrongArgumentException(
					'There is no such field "'.$dao->getParentIdField()
					.'" in map. See class '.get_class($dao)
				);
			}
		} elseif ($property->getRelationId() == MetaRelation::ONE_TO_ONE) {

			return new self(
				get_class($subject),
				$property->getName(),
				$property->getClassName(),
				'id',
				$property->getRelationId()
			);
		}
	}

	/**
	 * @param string $string
	 * @return ClassRelation
	 * @throws ClassNotFoundException
	 */
	public static function createFromString(string $string)
	{
		if(
			preg_match("/(\d):(\w+).(\w+)=(\w+).(\w+)/", $string, $res)
			&& count($res) == 6
		) {
			return new self($res[2], $res[3], $res[4], $res[5], $res[1]);
		} else
			throw new WrongArgumentException('Can`t create '.__CLASS__.' from string "'.$string.'"');

	}

	/**
	 * @return ObjectRelation
	 * @deprecated
	 */
	public static function invert(ConcreteReference $ref)
	{
		return ObjectRelation::createFromObject(
			$ref->getParent(),
			$ref->getParentProperty(),
			true
		);
	}

	/**
	 * @return LightMetaProperty
	 */
	public function getParentProtoProperty()
	{
		$subject = new $this->parentClass;

		return $subject->proto()->
			getPropertyByName($this->parentPropery);
	}

	/**
	 * @deprecated
	 */
	public function getParentClass()
	{
		return $this->parentClass;
	}

	/**
	 * @deprecated
	 */
	public function getParentProperty()
	{
		return $this->parentPropery;
	}

	/**
	 * @deprecated
	 */
	public function getParentField()
	{
		return $this->getParentProtoProperty()->getColumnName();
	}

	/**
	 * @deprecated
	 */
	public function getChildClass()
	{
		return $this->childClass;
	}

	/**
	 * @deprecated
	 */
	public function getChildProperty()
	{
		return $this->childProperty;
	}

	/**
	 * @deprecated
	 */
	public function getChildField()
	{
		return $this->getChildProtoProperty()->getColumnName();
	}

	/**
	 * @return LightMetaProperty
	 */
	public function getChildProtoProperty()
	{
		$class = $this->childClass;
		$subject = new $class;
		return $subject->proto()->getPropertyByName($this->childProperty);
	}

	/**
	 * @return MetaRelation
	 */
	public function getRelation()
	{
		return $this->relation;
	}

	/**
	 * @return bool
	 */
	public function isOneToOne()
	{
		return $this->relation->getId() == MetaRelation::ONE_TO_ONE;
	}

	/**
	 * @return bool
	 */
	public function isOneToMany()
	{
		return $this->relation->getId() == MetaRelation::ONE_TO_MANY;
	}

	/**
	 * @return bool
	 */
	public function isManyToMany()
	{
		return $this->relation->getId() == MetaRelation::MANY_TO_MANY;
	}

	/**
	 * @return string
	 */
	public function toString()
	{
		return $this->relationId.':'.$this->parentClass.'.'.$this->parentPropery.'='.$this->childClass.'.'.$this->childProperty;
	}

	/**
	 * @return string
	 */
	public function __toString()
	{
		return $this->toString();
	}
}
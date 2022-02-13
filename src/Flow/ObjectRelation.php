<?php

namespace sugrob\OnPHPToolkit\Flow;

use OnPHP\Core\Base\Enumeration;
use OnPHP\Core\Exception\ClassNotFoundException;
use OnPHP\Core\Exception\DatabaseException;
use OnPHP\Meta\Entity\MetaRelation;

class ObjectRelation extends ClassRelation
{
	const ONE_TO_ONE_INVERTED = 4;

	/**
	 * @var Prototyped
	 */
	protected $parent;

	public function __construct(
		int $parentId = null,
		string $parentClass,
		string $parentProperty,
		string $childClass,
		string $childProperty,
		int $relationId
	) {
		parent::__construct(
			$parentClass,
			$parentProperty,
			$childClass,
			$childProperty,
			$relationId
		);

		if (in_array(Enumeration::class, class_parents($parentClass))) {
			$this->parent = new $parentClass($parentId);
		} else {
			$parent = new $parentClass;

			if ($relationId == MetaRelation::ONE_TO_MANY) {
				if (!$parentId) {
					$this->parent = $parent;
				} else {
					try {
						$this->parent = $parent->dao()->getById($parentId);
					} catch (DatabaseException $e) {
						$this->parent = $parent->setId($parentId);
					}
				}
			} elseif ($relationId == MetaRelation::ONE_TO_ONE) {
				if (!$parentId) {
					throw new Exception(__CLASS__.'.'.__METHOD__.':Parent must be not null');
				} else {
					$this->parent = $parent->dao()->getById($parentId);
				}
			} elseif ($relationId == self::ONE_TO_ONE_INVERTED) {
				if (!$parentId) {
					throw new Exception(__CLASS__.'.'.__METHOD__.':Parent must be not null');
				}

				$child = new $this->childClass;
				$this->parent = $child->setId($parentId);
			}
		}
	}

	/**
	 * @throws ClassNotFoundException
	 * @param int $parentId
	 * @param string $parentClass
	 * @param string $parentProperty
	 * @param string $childClass
	 * @param string $childProperty
	 * @param int $relationId
	 * @return ObjectRelation
	 */
	public static function create(
		int $parentId,
		string $parentClass,
		string $parentProperty,
		string $childClass,
		string $childProperty,
		int $relationId
	)
	{
		return new self(
			$parentId,
			$parentClass,
			$parentProperty,
			$childClass,
			$childProperty,
			$relationId
		);
	}

	/**
	 * @throws ClassNotFoundException
	 * @param string $string
	 * @return ObjectRelation
	 */
	public static function createFromString($string)
	{
		if (
			preg_match("/(\d):(\w+).(\w+)\[(\w*)\]=(\w+).(\w+)/", $string, $res)
			&& count($res) == 7
		) {
			return new self($res[4], $res[2], $res[3], $res[5], $res[6], $res[1]);
		}

		throw new WrongArgumentException(
			'Can`t create '.__CLASS__.' from string "'.$string.'"'
		);
	}

	/**
	 * @throws ClassNotFoundException
	 * @param Prototyped $subject
	 * @param string $propertyName
	 * @param bool $inverted
	 * @return ObjectRelation|null
	 */
	public static function createFromObject(Prototyped $subject, $propertyName, $inverted = false)
	{
		if ($reference = parent::makeByObject($subject, $propertyName)) {

			$property = $subject->proto()->getPropertyByName($propertyName);

			if ($property->getRelationId() == MetaRelation::ONE_TO_MANY) {
				return new self(
					$subject->getId(),
					$reference->getParentClass(),
					$reference->getParentProperty(),
					$reference->getChildClass(),
					$reference->getChildProperty(),
					$reference->getRelationId()
				);
			} elseif ($property->getRelationId() == MetaRelation::ONE_TO_ONE) {
				if (!$inverted) {
					return new self(
						$subject->getId(),
						$reference->getParentClass(),
						$reference->getParentProperty(),
						$reference->getChildClass(),
						$reference->getChildProperty(),
						$reference->getRelationId()
					);
				} else {
					if ($linked = $subject->{$property->getGetter()}()) {
						return new self($linked->getId(),
							$reference->getParentClass(),
							$reference->getParentProperty(),
							$reference->getChildClass(),
							$reference->getChildProperty(),
							self::ONE_TO_ONE_INVERTED
						);
					}
				}
			}
		}
	}

	/**
	 * @return Prototyped
	 */
	public function getParent()
	{
		return $this->parent;
	}

	/**
	 * @return bool
	 */
	public function isInvertedOneToOne()
	{
		return $this->relation->getId() == self::ONE_TO_ONE_INVERTED;
	}

	/**
	 * @return string
	 */
	public function toString()
	{
		return $this->relationId.':'.$this->parentClass.'.'.$this->parentPropery.'['.$this->parent->getId().']='.$this->childClass.'.'.$this->childProperty;
	}
}
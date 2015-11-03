<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Tests\Orm\DataMappers\Mocks;

use Opulence\Databases\IConnection;
use Opulence\Orm\DataMappers\SqlDataMapper as BaseSqlDataMapper;
use Opulence\Orm\Ids\IntSequenceIdGenerator;
use Opulence\Orm\OrmException;

/**
 * Mocks the data mapper class for use in testing
 */
class SqlDataMapper extends BaseSqlDataMapper
{
    /** @var object[] The list of entities added */
    protected $entities = [];
    /** @var int The current Id */
    private $currId = 0;

    public function __construct()
    {
        $this->setIdGenerator();
    }

    /**
     * @inheritdoc
     */
    public function add(&$entity)
    {
        $this->currId++;
        $entity->setId($this->currId);
        $this->entities[$entity->getId()] = $entity;
    }

    /**
     * @inheritdoc
     */
    public function delete(&$entity)
    {
        unset($this->entities[$entity->getId()]);
    }

    /**
     * @inheritdoc
     */
    public function getAll()
    {
        // We clone all the entities so that they get new object hashes
        $clonedEntities = [];

        foreach (array_values($this->entities) as $entity) {
            $clonedEntities[] = clone $entity;
        }

        return $clonedEntities;
    }

    /**
     * @inheritdoc
     */
    public function getById($id)
    {
        if (!isset($this->entities[$id])) {
            throw new OrmException("No entity found with Id " . $id);
        }

        return clone $this->entities[$id];
    }

    /**
     * @return int
     */
    public function getCurrId()
    {
        return $this->currId;
    }

    /**
     * @inheritdoc
     */
    public function update(&$entity)
    {
        $this->entities[$entity->getId()] = $entity;
    }

    /**
     * @inheritdoc
     */
    protected function loadEntity(array $hash, IConnection $connection)
    {
        // Don't do anything
    }

    /**
     * @inheritdoc
     */
    protected function setIdGenerator()
    {
        $this->idGenerator = new IntSequenceIdGenerator("foo");
    }
} 
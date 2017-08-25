<?php

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2017 BuzzingPixel, LLC
 * @license Apache-2.0
 */

namespace BuzzingPixel\DataModel;

/**
 * Class ModelCollection
 */
class ModelCollection implements \Iterator, \Countable
{
    /** @var Model[] $models */
    private $models = array();

    /** @var array $keysToIndex */
    private $uuidToIndex = array();

    /** @var array $indexToKeys */
    private $indexToUuid = array();

    /** @var int $position */
    private $position = 0;

    /**
     * Constructor
     * @param Model[] $models
     */
    public function __construct($models = array())
    {
        // Set any models passed in
        $this->setModels($models);
    }

    /**
     * Rewind
     */
    public function rewind()
    {
        $this->position = 0;
    }

    /**
     * Current
     * @return Model
     */
    public function current()
    {
        return $this->models[$this->position];
    }

    /**
     * Key
     * @return int
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * Next
     */
    public function next()
    {
        ++$this->position;
    }

    /**
     * Valid
     * @return bool
     */
    public function valid()
    {
        return isset($this->models[$this->position]);
    }

    /**
     * Count
     */
    public function count()
    {
        return count($this->models);
    }

    /**
     * Add model
     * @param Model $model
     * @return self
     */
    public function addModel(Model $model)
    {
        // Add model indexed by its uuid
        $this->models[] = $model;

        // Re-index models
        $this->uuidToIndex = array();
        $this->indexToUuid = array();
        foreach ($this->models as $key => $model) {
            $this->uuidToIndex[$model->uuid] = $key;
            $this->indexToUuid[$key] = $model->uuid;
        }

        // Return instance
        return $this;
    }

    /**
     * Add models
     * @param Model[] $models
     * @return self
     */
    public function addModels($models)
    {
        // Iterate through models and add them
        foreach ($models as $model) {
            $this->addModel($model);
        }

        // Return instance
        return $this;
    }

    /**
     * Set models
     * @param Model[] $models
     * @return self
     */
    public function setModels($models)
    {
        // Empty the collection
        $this->emptyCollection();

        // Add the models
        $this->addModels($models);

        // Return instance
        return $this;
    }

    /**
     * Empty the collection
     * @return self
     */
    public function emptyCollection()
    {
        // Empty the collection
        $this->models = array();

        // Return instance
        return $this;
    }

    /**
     * Remove model
     * @param int|string|Model $model
     * @return self
     */
    public function removeModel($model)
    {
        // If $model is numeric, delete from the models array
        if (is_numeric($model)) {
            unset($this->models[$model]);

        // If $model is a string, it is assumed to be the uuid
        } elseif (gettype($model) === 'string') {
            unset($this->models[$this->uuidToIndex[$model]]);

        // If $model is instance of Model, get it's uuid and remove
        } elseif ($model instanceof Model) {
            unset($this->models[$this->uuidToIndex[$model->uuid]]);
        }

        // Return instance
        return $this;
    }

    /**
     * Pluck the value of property on all models
     * @param string $prop
     * @return array
     */
    public function pluck($prop)
    {
        // Return array
        $returnArray = array();

        // Iterate through each model and get the value
        foreach ($this->models as $model) {
            $returnArray[] = $model->{$prop};
        }

        // Return the array
        return $returnArray;
    }

    /**
     * As array
     * @param string $indexedBy
     * @return array
     */
    public function asArray($indexedBy = 'uuid')
    {
        // Return array
        $returnArray = array();

        // Iterate through models and get asArray
        foreach ($this->models as $model) {
            $returnArray[$model->{$indexedBy}] = $model->asArray();
        }

        // Return the array
        return $returnArray;
    }

    /**
     * Order models by property
     * @param string $prop
     * @param string $dir
     * @return self
     */
    public function orderBy($prop, $dir = 'asc')
    {
        // Make sure $dir is acceptable
        $dir = $dir === 'asc' || $dir === 'desc' ? $dir : 'asc';

        // Get array of models indexed by property
        $array = array();
        foreach ($this->models as $model) {
            $array[$model->{$prop}] = $model;
        }

        // Sort the array
        ksort($array);

        // Check the direction
        if ($dir === 'desc') {
            $array = array_reverse($array);
        }

        // Set the models
        $this->setModels(array_values($array));

        // Return instance
        return $this;
    }
}

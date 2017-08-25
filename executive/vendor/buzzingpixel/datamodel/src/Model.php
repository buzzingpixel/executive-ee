<?php

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2017 BuzzingPixel, LLC
 * @license Apache-2.0
 */

namespace BuzzingPixel\DataModel;

use BuzzingPixel\DataModel\Service\Generator\Uuid;

/**
 * Class Model
 *
 * @property-read string $uuid
 * @property-read bool $hasErrors
 * @property-read array $errors
 */
abstract class Model
{
    /** @var string HANDLER_NAMESPACE */
    const HANDLER_NAMESPACE = '\BuzzingPixel\DataModel\Service\DataHandler\\';

    /** @var string $uuid */
    private $uuid;

    /** @var bool $suppressWarnings */
    private $suppressWarnings = false;

    /** @var array $attributes */
    private $attributes = array();

    /** @var array $definedAttributes */
    private $definedAttributes = array();

    /** @var array $dirtyValues */
    private $dirtyValues = array();

    /** @var array $errors */
    private $errors = array();

    /**
     * Constructor
     * @param array $properties
     * @param bool $suppressWarnings
     */
    public function __construct(
        $properties = array(),
        $suppressWarnings = false
    ) {
        // Set the model's uuid
        $uuidService = new Uuid();
        $this->uuid = $uuidService->generate();

        // Set defined attributes
        $this->setDefinedAttributes($this->defineAttributes());

        // Set properties
        $this->setProperties($properties);

        // Set suppress warnings
        $this->suppressWarnings = $suppressWarnings;
    }

    /**
     * Clone
     */
    public function __clone()
    {
        // Set the model's uuid
        $uuidService = new Uuid();
        $this->uuid = $uuidService->generate();
    }

    /**
     * Get uuid
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * Define attributes
     * @return array
     */
    protected function defineAttributes()
    {
        return array();
    }

    /**
     * Set defined attributes
     * @param array $attributes
     * @param bool $clearPreviousAttributes
     * @return self
     */
    public function setDefinedAttributes(
        $attributes = array(),
        $clearPreviousAttributes = true
    ) {
        // Check if we should clear out any previously defined attributes
        if ($clearPreviousAttributes) {
            $this->definedAttributes = array();
        }

        // Iterate through attributes and set as defined
        foreach ($attributes as $name => $def) {
            // If the def type is a string, make it an array of type
            if (gettype($def) === 'string') {
                $def = array(
                    'type' => $def
                );
            }

            // If type is not defined, the attribute is not valid
            if (gettype($def) !== 'array' || ! isset($def['type'])) {
                continue;
            }

            // Set the defined attribute
            $this->definedAttributes[$name] = $def;
        }

        // Return instance
        return $this;
    }

    /**
     * Get defined attributes
     */
    public function getDefinedAttributes()
    {
        return $this->definedAttributes;
    }

    /**
     * Magic setter
     * @param $name
     * @param $val
     */
    public function __set($name, $val)
    {
        // Send to the setter
        $this->setProperty($name, $val);
    }

    /**
     * Check for setter
     * @param string $name
     * @param mixed $val
     * @return array
     */
    private function checkForSetter($name, $val)
    {
        // Set the method to use
        $method = 'set' . ucfirst($name);

        // Check for method
        if (! method_exists($this, $method)) {
            return array(
                'methodExists' => false,
                'val' => null
            );
        }

        // Run the method and return the value
        return array(
            'methodExists' => true,
            'val' => $this->{$method}($val)
        );
    }

    /**
     * Setter
     * @param string $name
     * @param mixed $val
     * @return self
     */
    public function setProperty($name, $val)
    {
        // Make sure this is a property we can set
        if (! isset($this->definedAttributes[$name])) {
            // Check for custom setter
            $customSetter = $this->checkForSetter($name, $val);

            // If there was a custom setter method, return instance
            if ($customSetter['methodExists']) {
                return $this;
            }

            // Set a warning if we are not suppressing warnings
            if (! $this->suppressWarnings) {
                trigger_error("Model property {$name} is not defined");
            }

            // Return instance
            return $this;
        }

        // Preserve the dirty value
        $dirtyVal = $val;

        // Get this property type
        $type = $this->definedAttributes[$name]['type'];

        // Get type handler class
        $typeHandlerClass = ucfirst($type) . 'Handler';

        // Set custom handler class name
        $handlerNamespace = self::HANDLER_NAMESPACE;
        $customHandlerClass = "{$handlerNamespace}{$typeHandlerClass}";

        // Check for custom handler class
        if (class_exists($customHandlerClass) &&
            defined("{$customHandlerClass}::SET_HANDLER")
        ) {
            // Create instance of handler class
            $handler = new $customHandlerClass;

            // Run specified method
            $val = $handler->{$handler::SET_HANDLER}(
                $val,
                $this->definedAttributes[$name]
            );
        }

        // Get this class
        $thisClass = get_class($this);

        // Get custom handlers
        $customHandlers = defined("{$thisClass}::CUSTOM_HANDLERS") ?
            $thisClass::CUSTOM_HANDLERS :
            array();

        // Check for custom handler
        if (defined("{$thisClass}::CUSTOM_HANDLERS") &&
            isset($customHandlers[$type])
        ) {
            // Get the handler class string
            $handlerClassString = $thisClass::CUSTOM_HANDLERS[$type];

            // Create instance of handler class
            $handler = new $handlerClassString();

            // Run specified method if defined
            if (defined("{$handlerClassString}::SET_HANDLER")) {
                $val = $handler->{$handler::SET_HANDLER}(
                    $val,
                    $this->definedAttributes[$name]
                );
            }
        }

        // Check for custom setter
        $customSetter = $this->checkForSetter($name, $val);

        // Get the custom setter val if it exists
        if ($customSetter['methodExists']) {
            $val = $customSetter['val'];
        }

        // Set property
        $this->attributes[$name] = $val;

        // Save the dirty value
        $this->dirtyValues[$name] = $dirtyVal;

        // Return instance
        return $this;
    }

    /**
     * Set attributes
     * @param array $properties
     * @return self
     */
    public function setProperties($properties = array())
    {
        // Make sure incoming properties var is iterable
        if (! gettype($properties) === 'array') {
            return $this;
        }

        // Go through each property, and if it is a defined attribute, set it
        foreach ($properties as $name => $val) {
            // Check if the attribute is defined for this model
            if (! isset($this->definedAttributes[$name])) {
                continue;
            }

            // Set the property
            $this->setProperty($name, $val);
        }

        // Return instance
        return $this;
    }

    /**
     * Magic getter
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        // Use the getter
        return $this->getProperty($name);
    }

    /**
     * Check for getter
     * @param string $name
     * @param mixed $existingVal
     * @return array
     */
    private function checkForGetter($name, $existingVal = null)
    {
        // Set the method to use
        $method = 'get' . ucfirst($name);

        // Check for method
        if (! method_exists($this, $method)) {
            return array(
                'methodExists' => false,
                'val' => null
            );
        }

        // Run the method and return the value
        return array(
            'methodExists' => true,
            'val' => $this->{$method}($existingVal)
        );
    }

    /**
     * Get property
     * @param string $name
     * @return mixed
     */
    public function getProperty($name)
    {
        // Make sure this is a property we can get
        if (! isset($this->definedAttributes[$name])) {
            // Check for custom getter
            $customGetter = $this->checkForGetter($name);

            // If the custom getter method exists, return the value
            if ($customGetter['methodExists']) {
                return $customGetter['val'];
            }

            // Set a warning if we are not suppressing warnings
            if (! $this->suppressWarnings) {
                trigger_error("Model property {$name} is not defined");
            }

            // Return null since we have no property and no getter
            return null;
        }

        // Check if property has been set
        if (! isset($this->attributes[$name])) {
            // Check for custom getter
            $customGetter = $this->checkForGetter($name);

            // If the custom getter method exists, return the value
            if ($customGetter['methodExists']) {
                return $customGetter['val'];
            }

            return null;
        }

        // Set a val variable
        $val = $this->attributes[$name];

        // Get this property type
        $type = $this->definedAttributes[$name]['type'];
        $typeHandlerClass = ucfirst($type) . 'Handler';

        // Set custom handler class name
        $handlerNamespace = self::HANDLER_NAMESPACE;
        $customHandlerClass = "{$handlerNamespace}{$typeHandlerClass}";

        // Check for custom handler class
        if (class_exists($customHandlerClass) &&
            defined("{$customHandlerClass}::GET_HANDLER")
        ) {
            // Create instance of handler class
            $handler = new $customHandlerClass;

            // Run specified method
            $val = $handler->{$handler::GET_HANDLER}(
                $val,
                $this->definedAttributes[$name]
            );
        }

        // Get this class
        $thisClass = get_class($this);

        // Get custom handlers
        $customHandlers = defined("{$thisClass}::CUSTOM_HANDLERS") ?
            $thisClass::CUSTOM_HANDLERS :
            array();

        // Check for custom handler
        if (defined("{$thisClass}::CUSTOM_HANDLERS") &&
            isset($customHandlers[$type])
        ) {
            // Get the handler class string
            $handlerClassString = $thisClass::CUSTOM_HANDLERS[$type];

            // Create instance of handler class
            $handler = new $handlerClassString();

            // Run specified method if defined
            if (defined("{$handlerClassString}::GET_HANDLER")) {
                $val = $handler->{$handler::GET_HANDLER}(
                    $val,
                    $this->definedAttributes[$name]
                );
            }
        }

        // Check for custom getter
        $customGetter = $this->checkForGetter($name, $val);

        // If the custom getter method exists, get the value
        if ($customGetter['methodExists']) {
            $val = $customGetter['val'];
        }

        // Return the value
        return $val;
    }

    /**
     * Get dirty value
     * @param string $name
     * @return mixed
     */
    public function getDirtyValue($name)
    {
        // Return the dirty value if it is set
        if (isset($this->dirtyValues[$name])) {
            return $this->dirtyValues[$name];
        }

        // No value exists
        return null;
    }

    /**
     * As array
     * @return array
     */
    public function asArray()
    {
        // Return array
        $returnArray = array(
            'uuid' => $this->uuid
        );

        // Iterate through the defined attributes
        foreach ($this->definedAttributes as $attribute => $def) {
            // Get the property value
            $val = $this->{$attribute};

            // Get this property type
            $type = $def['type'];
            $typeHandlerClass = ucfirst($type) . 'Handler';

            // Set custom handler class name
            $handlerNamespace = self::HANDLER_NAMESPACE;
            $custHandlerClass = "{$handlerNamespace}{$typeHandlerClass}";

            // Check for custom handler class
            if (class_exists($custHandlerClass) &&
                defined("{$custHandlerClass}::AS_ARRAY_HANDLER")
            ) {
                // Create instance of handler class
                $handler = new $custHandlerClass;

                // Run specified method
                $val = $handler->{$handler::AS_ARRAY_HANDLER} ($val, $def);
            }

            // Get this class
            $thisClass = get_class($this);

            // Get custom handlers
            $customHandlers = defined("{$thisClass}::CUSTOM_HANDLERS") ?
                $thisClass::CUSTOM_HANDLERS :
                array();

            // Check for custom handler
            if (defined("{$thisClass}::CUSTOM_HANDLERS") &&
                isset($customHandlers[$type])
            ) {
                // Get the handler class string
                $handlerClassString = $thisClass::CUSTOM_HANDLERS[$type];

                // Create instance of handler class
                $handler = new $handlerClassString();

                // Run specified method if defined
                if (defined("{$handlerClassString}::AS_ARRAY_HANDLER")) {
                    $val = $handler->{$handler::AS_ARRAY_HANDLER}($val, $def);
                }
            }

            // Add val to array
            $returnArray[$attribute] = $val;
        }

        // Return the array
        return $returnArray;
    }

    /**
     * Validate model
     * @return bool
     */
    public function validate()
    {
        // Set an errors array
        $errors = array();

        // Iterate through model properties
        foreach ($this->definedAttributes as $attribute => $def) {
            $val = $this->{$attribute};

            // Get the potential custom validation method name
            $customMethod = 'validate' . ucfirst($attribute);

            // Check if there is a custom validation method
            if (method_exists($this, $customMethod)) {
                // Run specified method to get validation errors
                $validationErrors = $this->{$customMethod}($val, $def);

                // Set validation errors if there are any
                if ($validationErrors) {
                    $errors[$attribute] = $validationErrors;
                }

                // Custom method handled it for us, continue
                continue;
            }

            // Get this property type
            $type = $def['type'];
            $typeHandlerClass = ucfirst($type) . 'Handler';

            // Set custom handler class name
            $handlerNamespace = self::HANDLER_NAMESPACE;
            $custHandlerClass = "{$handlerNamespace}{$typeHandlerClass}";

            // Check for custom handler class
            if (class_exists($custHandlerClass) &&
                defined("{$custHandlerClass}::VALIDATION_HANDLER")
            ) {
                // Create instance of handler class
                $handler = new $custHandlerClass;

                // Run specified method to get validation errors
                $validationErrors = $handler->{$handler::VALIDATION_HANDLER}(
                    $val,
                    $def
                );

                // Set validation errors if there are any
                if ($validationErrors) {
                    $errors[$attribute] = $validationErrors;
                }

                // Custom handler handled it for us, continue
                continue;
            }

            // Get this class
            $thisClass = get_class($this);

            // Get custom handlers
            $customHandlers = defined("{$thisClass}::CUSTOM_HANDLERS") ?
                $thisClass::CUSTOM_HANDLERS :
                array();

            // Check for custom handler
            if (defined("{$thisClass}::CUSTOM_HANDLERS") &&
                isset($customHandlers[$type])
            ) {
                // Get the handler class string
                $handlerClassString = $thisClass::CUSTOM_HANDLERS[$type];

                // Create instance of handler class
                $handler = new $handlerClassString();

                // Run specified method if defined
                if (defined("{$handlerClassString}::VALIDATION_HANDLER")) {
                    // Get validation errors return
                    $validationErrors = $handler->{$handler::VALIDATION_HANDLER}(
                        $val,
                        $def
                    );

                    // Set validation errors if there are any
                    if ($validationErrors) {
                        $errors[$attribute] = $validationErrors;
                    }

                    // Custom handler handled it for us, continue
                    continue;
                }
            }

            // Since there was no custom handler, validate if required
            if (isset($def['required']) && $def['required'] && ! $val) {
                $errors[$attribute][] = 'This field is required';
            }
        }

        // Set the errors
        $this->errors = $errors;

        // Return result
        return ! $this->hasErrors;
    }

    /**
     * Has errors
     */
    public function getHasErrors()
    {
        return count($this->errors) !== 0;
    }

    /**
     * Get errors
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Add error
     * @param string $attribute
     * @param string $message
     */
    public function addError($attribute, $message)
    {
        // Get defined attributes
        $attributes = $this->getDefinedAttributes();

        // Make sure the specified attribute is attached to the model
        if (! isset($attributes[$attribute])) {
            return;
        }

        // Start off with an empty array for existing errors
        $attrErrors = array();

        // If the model already has errors for this attribute, get them
        if (isset($this->errors[$attribute])) {
            $attrErrors = $this->errors[$attribute];
        }

        // Add the error to the array
        $attrErrors[] = $message;

        // Add the errors to the model
        $this->errors[$attribute] = $attrErrors;
    }
}

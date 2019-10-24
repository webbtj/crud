<?php

namespace Webbtj\Crud;

use Illuminate\Support\Str;
use Webbtj\Crud\Util;
use Doctrine\DBAL\Types\Type;

class Field
{
    private $columnName;
    private $type;
    private $className;
    private $displayName;
    private $options;
    private $readOnly;
    private $views;

    public function __construct(
        String $columnName,
        String $className,
        String $type = null,
        String $tableName = null,
        Array $views = null
    ) {
        $this->setColumnName($columnName);
        $this->setClassName($className);
        $this->setViews($views);
        if ($type) {
            $this->setType($type);
        }
        if (('array' == $this->type || 'simple_array' == $this->type || 'enum' == $this->type) && $tableName) {
            $this->options = Util::enumOptions($tableName, $columnName);
        }
    }

    public function setColumnName(String $columnName)
    {
        $this->columnName = $columnName;
        $this->displayName = snake_to_title($columnName);
    }
    public function getColumnName()
    {
        return $this->columnName;
    }

    private function setType(String $type)
    {
        $type = strtolower($type);
        if (Str::startsWith($type, 'decimal')) {
            $type = 'decimal';
        }

        switch ($type) {
            case 'integer':
            case 'int':
            case 'smallint':
            case 'bigint':
                $this->type = 'integer';
                break;
            case 'real':
            case 'float':
            case 'double':
                $this->type = 'float';
                break;
            case 'decimal':
                $this->type = 'decimal';
                break;
            case 'string':
            case 'guid':
                $this->type = 'string';
                break;
            case 'text':
            case 'blob':
                $this->type = 'text';
                break;
            case 'boolean':
            case 'bool':
            case 'binary':
                $this->type = 'boolean';
                break;
            case 'object':
                $this->type = 'object';
                break;
            case 'array':
            case 'simple_array':
                $this->type = 'array';
                break;
            case 'enum':
                $this->type = 'enum';
                break;
            case 'json':
            case 'json_array':
                $this->type = 'json';
                break;
            case 'collection':
                $this->type = 'collection';
                break;
            case 'date':
            case 'date_immutable':
                $this->type = 'date';
                break;
            case 'time':
            case 'time_immutable':
                $this->type = 'time';
                break;
            case 'datetime':
            case 'custom_datetime':
            case 'datetime_immutable':
            case 'datetimetz':
            case 'datetimetz_immutable':
                $this->type = 'datetime';
                break;
            case 'timestamp':
                $this->type = 'timestamp';
                break;
        }
    }
    public function getType()
    {
        return $this->type;
    }

    public function setClassName(String $className)
    {
        $this->className = $className;
        $types = array_flip(Type::getTypesMap());
        if (array_key_exists($className, $types)) {
            $this->setType($types[$className]);
        }
    }
    public function getClassName()
    {
        return $this->className;
    }

    public function getDisplayName()
    {
        return $this->displayName;
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function setReadOnly(bool $readOnly)
    {
        $this->readOnly = $readOnly;
    }

    public function getReadOnly()
    {
        return $this->readOnly;
    }

    public function setViews(Array $views)
    {
        $this->views = $views;
    }
    public function getViews()
    {
        return $this->views;
    }
}

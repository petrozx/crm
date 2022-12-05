<?php
namespace core\persist;

class Persist
{

    private string $tableName;
    private array $columns;
    private array $values;
    private array $methods;
    private object $obj;
    private string $className;

    /**
     * @param object $obj
     */
    public function __construct(object $obj)
    {
        $this->obj = $obj;
        $this->className = get_class($obj);
        $this->methods = get_class_methods($this->obj);
        $this->tableName = $this->_getTableName();
    }

    private function _getCombine(): array
    {
        return array_combine(
            array_map(fn($k) => $this->camelToUnder($k), array_values($this->columns)),
            array_values($this->values)
        );
    }

    private function _getTableName(): string
    {
        $cl = explode('\\', get_class($this->obj));
        return $this->camelToUnder($cl[count($cl) - 1]);
    }

    private function getColumns(): array|string
    {
        return str_replace('set', '',
            array_filter($this->methods, fn($k) =>
                str_contains($k, 'set') && $k !== 'setId'
            )
        );
    }

    private function getValues(): array
    {
        $currentObj = $this->obj;
        return array_map(function ($k) use ($currentObj) {
            $methName = "get$k";
            return $currentObj->$methName();
        }, $this->columns);
    }

    private function camelToUnder(string $str): string
    {
        return ltrim(
            strtolower(
                preg_replace('/[A-Z]([A-Z](?![a-z]))*/',
                    '_$0',
                    $str
                )
            ),
            '_'
        );
    }

    /**
     * @return string
     */
    public function getTableName(): string
    {
        return $this->tableName;
    }

    /**
     * @return array
     */
    public function getCombine(): array
    {
        $this->columns = $this->getColumns();
        $this->values = $this->getValues();
        return $this->_getCombine();
    }

    public function build(mixed $queryResult): object|bool
    {
        if (array_key_exists(0, $queryResult)) {
            $queryResult = $queryResult[0];
        } else if (count($queryResult) === 0) {
            throw new \RuntimeException("При поиске обьекта, что-то пошло не так.");
        }
        foreach ($queryResult as $key => $value) {
            $methodName = 'set' . $this->camelize($key);
            $this->obj->$methodName($value);
        }
            return $this->obj;
    }

    public function buildAll(array $queryResult): array
    {
        return array_map(fn($k) => clone $this->build($k), $queryResult);
    }

    private function camelize($input): array|string
    {
        return str_replace('_', '', ucwords($input, '_'));
    }

    public function setId(int $id): object
    {
        $this->obj->setId($id);
        return $this->obj;
    }
}
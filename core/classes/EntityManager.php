<?php
namespace core\classes;
use core\database\DBO;
use core\interfaces\CRUD;
use core\persist\Persist;

/**
 * Класс родитель для всех Entity.
 * Для наследника необходимо наличие методо get и set,
 * для всех приватный полей.
 * Конструктор должен всегда быть пустой,
 * либо со свойствами присваивающемся по умолчанию.
 */
class EntityManager implements CRUD
{

    private DBO $db;

    private Persist $persist;

    private object $obj;

    public function __construct(object $obj, DBO $db)
    {
        $this->obj = $obj;
        $this->db = $db;
        $this->persist = new Persist($obj);
    }

    /**
     * @return object|string
     * Метод вызывается на новом экземпляре класса.
     * Метод вызывается на новом экземпляре класса.
     * Задавать значение @id не нужно.
     */
    public function save(): object|string
    {
        try {
            return $this->persist->setId(
                $this->db->setTable($this->persist->getTableName())
                ->beginTransaction()
                ->insert($this->persist->getCombine())
                ->commitTransaction()
                ->getResultInsert()
            );
        } catch (\Exception $e) {
            return $e->getCode();
        }
    }

    /**
     * @param array $arr
     * @return object|bool
     * Метод вызывается на новом экзмпляре класса.
     */
    public function find(array $arr): object|string
    {
        try {
            return $this->persist->build(
                $this->db->setTable($this->persist->getTableName())
                ->beginTransaction()
                ->select(['*'], $arr)
                ->commitTransaction()
                ->getResultSelect()
            );
        } catch (\Exception) {
            return 'Сущность с такими характеристиками, не найдена';
        }
    }

    /**
     * @param array|null $arr
     * @return array
     * Метод может вызываться, как на созданном экземпляре, так и на новом, передав фильтр,
     * по которому следует найти и удалить запись.
     */
    public function delete(array $arr = null): array
    {
        try {
            $this->db->setTable($this->persist->getTableName())
                ->beginTransaction()
                ->delete($arr ?: ["id" => $this->obj->getId()])
                ->commitTransaction();
            return ['result' => true];
        } catch (\Exception $e) {
            return ['result' => false,
                'message'=>$e->getCode()
            ];
        }
    }

    /**
     * @return bool[]
     * Метод вызывается на существующем экземляре класса.
     */
    public function update(): array
    {
        try {
            $this->db->setTable($this->persist->getTableName())
                ->beginTransaction()
                ->update($this->persist->getCombine(), $this->obj->getId())
                ->commitTransaction();
            return ['result' => true];
        }catch (\PDOException $e) {
            return ['result' => false,
                    'message'=>$e->getCode()
            ];
        }
    }

    /**
     * @param array|null $arr
     * @return array
     * Метод вызывается на новом экземпляре класса.
     */
    public function findAll(array $arr = null): array
    {
        try {
            return $this->persist->buildAll(
                $this->db->setTable($this->persist->getTableName())
                    ->beginTransaction()
                    ->select(['*'], $arr)
                    ->commitTransaction()
                    ->getResultSelect()
            );
        } catch (\Exception) {
            return [];
        }
    }

}
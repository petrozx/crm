<?php
namespace core\classes;
use core\database\DBO;
use core\interfaces\CRUD;
use core\persist\Persist;
use Exception;

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

    private array $prepare;

    public function __construct(object $obj, DBO $db, $prepare)
    {
        $this->obj = $obj;
        $this->db = $db;
        $this->persist = new Persist($obj);
        $this->prepare = $prepare;
    }

    /**
     * @return object|string
     * Метод вызывается на новом экземпляре класса.
     * Метод вызывается на новом экземпляре класса.
     * Задавать значение @id не нужно.
     * @throws Exception
     */
    public function save(): object|string
    {
        try {
            return $this->persist->setId(
                $this->db->setTable($this->persist->getTableName())
                ->beginTransaction()
                ->insert($this->prepare)
                ->commitTransaction()
                ->getResultInsert()
            );
        } catch (Exception) {
            throw new Exception("При попытке сохранить запись, произошла ошибка");
        }
    }

    /**
     * @param array $arr
     * @return object|bool
     * Метод вызывается на новом экзмпляре класса.
     * @throws Exception
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
        } catch (Exception) {
            throw new Exception('Запись с такими характеристиками, не найдена');
        }
    }

    /**
     * @param array|null $arr
     * @return bool
     * Метод может вызываться, как на созданном экземпляре, так и на новом, передав фильтр,
     * по которому следует найти и удалить запись.
     * @throws Exception
     */
    public function delete(array $arr = null): bool
    {
        try {
            $this->db->setTable($this->persist->getTableName())
                ->beginTransaction()
                ->delete($arr ?: ["id" => $this->obj->getId()])
                ->commitTransaction();
            return true;
        } catch (Exception) {
            throw new Exception('При попытке удалить запись, произошла ошибка');
        }
    }

    /**
     * @return bool
     * Метод вызывается на существующем экземляре класса.
     * @throws Exception
     */
    public function update(): bool
    {
        try {
            $this->db->setTable($this->persist->getTableName())
                ->beginTransaction()
                ->update($this->persist->getCombine(), $this->obj->getId())
                ->commitTransaction();
            return true;
        }catch (Exception) {
            throw new Exception("При попытке обновить запись, произошла ошибка");
        }
    }

    /**
     * @param array $arr
     * @return array
     * Метод вызывается на новом экземпляре класса.
     * @throws Exception
     */
    public function findAll(array $arr): array
    {
        try {
            return $this->persist->buildAll(
                $this->db->setTable($this->persist->getTableName())
                    ->beginTransaction()
                    ->select(['*'], $arr)
                    ->commitTransaction()
                    ->getResultSelect()
            );
        } catch (Exception) {
            throw new Exception('Записи с такими характеристиками, не найдены');
        }
    }

}
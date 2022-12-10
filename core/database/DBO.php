<?php
namespace core\database;
use PDO;
use core\traits\Singleton;

class DBO
{
    use Singleton;
    private PDO $connect;
    private String $table;
    private array $resultSelect;

    private function __construct()
    {
        $opt = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        $this->connect = new PDO(
            sprintf('pgsql:host=%1$s;
                port=%2$s;
                dbname=%3$s;',
                getenv('DBHOST'),
                getenv('DBPORT'),
                getenv('DBNAME')
            ),
            getenv('DBUSER'),
            getenv('DBPASS'),
            $opt);
    }

    public function setTable($tableName): static
    {
        $this->table = $tableName;
        return $this;
    }
    public function beginTransaction(): static
    {
        $this->connect->beginTransaction();
        return $this;
    }
    public function insert($arr): static
    {
        $names = implode(',', array_keys($arr));
        $preValues = implode(', ',array_map(fn($el) => ":$el", array_keys($arr)));
        $prepare = $this->connect->prepare(sprintf("insert into %s (%s) values (%s)",
            $this->table,
            $names,
            $preValues
        ));
        var_dump($arr);
        $prepare->execute($arr);
        $prepare->fetch();
        return $this;
    }

    public function update($arr, $id): static
    {
        $preValues = implode(', ',array_map(fn($el) => "$el=:$el", array_keys($arr)));
        $sql = sprintf('UPDATE %s SET %s WHERE id=%s',
            $this->table,
            $preValues,
            $id,
        );
        $prepare = $this->connect->prepare($sql);
        $prepare->execute($arr);
        $prepare->fetch();
        return $this;
    }
    public function delete(array $arr): static
    {
        $preValues = implode(', ',array_map(fn($el) => "$el=:$el", array_keys($arr)));
        $sql = sprintf('DELETE FROM %s WHERE %s',
            $this->table,
            $preValues,
        );
        $prepare = $this->connect->prepare($sql);
        $prepare->execute($arr);
        $prepare->fetch();
        return $this;
    }
    public function select($select, $where=null, $order=null, $group_by=null): static
    {
        $newWhere = [];
        $columns = implode(',', $select);
        $filter = '';
        $groupBy = '';
        $orderSql = '';
        if (!empty($where)) {
            $filter = 'where '.implode(' and ', array_map(function($k) {
                    $column = preg_replace('/[<=>]/', '', $k);
                    $eq = preg_replace('/[a-zA-Z_]/', '', $k);
                    return "$column $eq :$column";
                },
                    array_keys($where)
                ));

            foreach ($where as $key=>$value) {
                $newWhere[preg_replace('/[<=>]/', '', $key)] = $value;
            }
            $where = $newWhere;
        }

        if(!empty($order)) {
            $column = implode('', array_keys($order));
            $option = implode('', array_values($order));
            $orderSql = sprintf("order by %s %s", $column, $option);
        }

        if(!empty($group_by)) {
            $groupBy = sprintf("group by %s", $group_by);
        }

        $prepare = $this->connect->prepare(query:
            trim(
                sprintf("select %s from %s %s %s %s",
                    $columns,
                    $this->table,
                    $filter,
                    $groupBy,
                    $orderSql,
                )
            )
        );
        $prepare->execute($where);
        $this->resultSelect = $prepare->fetchAll();
        return $this;
    }
    public function commitTransaction(): static
    {
        $this->connect->commit();
        return $this;
    }

    /**
     * @return array
     */
    public function getResultSelect(): array
    {
        return $this->resultSelect;
    }

    /**
     * @return int
     */
    public function getResultInsert(): int
    {
        return $this->connect->lastInsertId();
    }

    public function showColumns(): bool|array
    {
        return $this->connect->query(sprintf("SELECT *
                                                    FROM information_schema.columns
                                                    WHERE table_schema = 'public'
                                                    AND table_name = '%s'", $this->table))
            ->fetchAll();
    }
}
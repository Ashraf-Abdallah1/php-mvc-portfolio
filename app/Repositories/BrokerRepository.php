<?php

namespace App\Repositories;

use App\Models\Broker;
use App\Repositories\BrokerRepositoryInterface;
use Framework\Database;
use phpDocumentor\Reflection\Project;
use phpDocumentor\Reflection\Types\This;

class BrokerRepository implements BrokerRepositoryInterface
{
    private Database $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    /**
     * @inheritDoc
     */
    public function all(): array
    {
        $stmt = $this->database->run("SELECT * FROM brokers")->fetchAll();

        $brokers = [];
        foreach ($stmt as $row) {
            $broker = $this->fromDbRow($row);
            $brokers [] = $broker;
        }
        return $brokers;
    }

    public function find(int $id): ?Broker
    {
        $stmt = $this->database->run("select * from brokers where id = :id", ['id' => $id])->fetch();
        return $this->fromDbRow($stmt);
    }

    public function insert(Broker $broker): ?Broker
    {
        $stmt = $this->database->run("insert into brokers (name) values (:name)", [
            "name" => $broker->name
        ]);
        $broker->id = $this->database->getLastID();
        return $broker;
    }

    public function update(Broker $broker): bool
    {
        $stmt = $this->database->run("update brokers set name =:name where id = :id", [
            "name" => $broker->name,
            "id" => $broker->id
        ]);
        return $stmt->rowCount() > 0;
    }

    public function delete(int $id): bool
    {

        $stmt = $this->database->run("delete from brokers where id = :id", ['id' => $id]);

//        var_dump($stmt->rowCount());
//        die();
        return $stmt->rowCount() > 0;
    }

    private function fromDbRow(mixed $row): Broker
    {

        $broker = new Broker();
        if ($row->id !== null) {
            $broker->id = $row->id;
        }
        $broker->name = $row->name;
        if ($row->agent !== null) {
            $broker->agents = $row->agents;
        }

        return $broker;
    }
}

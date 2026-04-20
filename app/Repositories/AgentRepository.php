<?php

namespace App\Repositories;

use App\Models\Agent;
use App\Repositories\AgentRepositoryInterface;
use Framework\Database;

class AgentRepository implements AgentRepositoryInterface
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
        $stmt = $this->database->run("SELECT * FROM agents")->fetchAll();
        $agents = [];
        foreach ($stmt as $row) {
            $agent = $this->fromDbRow($row);
            $agents [] = $agent;
        }
        return $agents;
    }

    public function find(int $id): ?Agent
    {
        return null;
    }

    public function insert(Agent $agent): Agent
    {
        return $agent;
    }

    public function update(Agent $agent): bool
    {
        return false;
    }

    public function delete(int $id): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function brokerAgents(int $brokerId): array
    {
        $stmt = $this->database->run("Select * from Agents where broker_id = :brokerId", [
            "brokerId" => $brokerId])->fetchAll();
        $agents = [];
        foreach ($stmt as $row) {
            $agent = $this->fromDbRow($row);
            $agents[] = $agent;
        }
        return $agents;
    }

    private function fromDbRow(mixed $row): Agent
    {
        $agent = new Agent();
        $agent->id = $row->id;
        $agent->name = $row->name;
        $agent->stage = $row->stage;
        $agent->level = $row->level;
        $agent->brokerId = $row->broker_id;
        return $agent;
    }
}

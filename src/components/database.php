<?php
/**
 * Database Class.
 * This class provides methods for connecting to a local sqlite database.
 * 
 * @package Gtt\Api\Components
 * @author Sebastiano Racca
 */
declare(strict_types=1);

namespace GTT\Api\Components;

use \PDO;

final class Database{
    protected $pdo;

    public function __construct(string $path){
        $this->pdo = new PDO("sqlite:$path");
        $this->pdo->query('CREATE TABLE IF NOT EXISTS stops(
            stop INT,
            line INT,
            hour VARCHAR(10),
            realtime BOOL,
            date DATETIME
        )');
    }

    /**
     * Execute a function catching any PDO Exception
     * 
     * @param callable $function The function to be executed.
     * 
     * @return mixed Returns the value of the function or an object when an Exception is catched.
     */
    public function execute(callable $function, bool $needs_pdo): mixed{

        try{
            return $function($needs_pdo ? $this->pdo : null);

        } catch(\PDOException $e){
            return (object)["ok" => false, "error" => $e->getMessage()];
        }

    }
}
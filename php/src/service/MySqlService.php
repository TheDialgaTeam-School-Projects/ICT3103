<?php

namespace teamwork\service;

use mysqli;
use mysqli_result;

class MySqlService
{
    /** @var mysqli Mysqli object. */
    private mysqli $connection;

    /**
     * MySqlService constructor.
     * @param array $dbConfig Database configuration.
     */
    public function __construct(array $dbConfig)
    {
        $this->connection = new mysqli($dbConfig['Host'], $dbConfig['Username'], $dbConfig['Password'], $dbConfig['Schema'], $dbConfig['Port']);
        $this->initCheck($dbConfig);
    }

    /**
     * Initialization checks.
     * @param array $dbConfig Database configuration.
     */
    private function initCheck(array $dbConfig): void
    {
        // Check if the database is initialized.
        $result = $this->query("SELECT count(*) FROM information_schema.tables WHERE table_schema = ?", $dbConfig['Schema']);
        $count = $result->fetch_array(MYSQLI_NUM)[0];
        $result->close();

        if ($count > 0) return;

        // Database is not initialize, maybe it is a good time to do so now while we still can :P
        $this->query("CREATE TABLE `user_account` (`username` VARCHAR(255) NOT NULL, `password` VARCHAR(255) NOT NULL, `phone` INT NOT NULL, `first_name` VARCHAR(255) NOT NULL, `last_name` VARCHAR(255) NOT NULL, `date_of_birth` DATE NOT NULL, PRIMARY KEY (`username`)) ENGINE = InnoDB;");
        $this->query("CREATE TABLE `logger` (`id` INT NOT NULL AUTO_INCREMENT, `username` VARCHAR(255) NOT NULL, `logged_datetime` DATETIME NOT NULL, PRIMARY KEY (`id`), INDEX `fk_logger_user_account_idx` (`username` ASC) VISIBLE, CONSTRAINT `fk_logger_user_account` FOREIGN KEY (`username`) REFERENCES `teamwork`.`user_account` (`username`) ON DELETE CASCADE ON UPDATE CASCADE) ENGINE = InnoDB;");
    }

    /**
     * Request a query from the database.
     * @param string $query Prepared query.
     * @param mixed ...$params Parameters.
     * @return false|mysqli_result Returns a resultset or FALSE on failure.
     */
    public function query(string $query, ...$params)
    {
        if ($this->connection->connect_error) return false;

        $statement = $this->connection->prepare($query);
        if (!$statement) return false;

        if (count($params) > 0) {
            $paramTypes = $this->getParamTypes($params);
            $temp = [$paramTypes];

            foreach ($params as &$value) {
                $temp[] = &$value;
            }

            $bindResult = call_user_func_array([$statement, 'bind_param'], $temp);
            if (!$bindResult) return false;
        }

        $executeResult = $statement->execute();
        if (!$executeResult) return false;

        $result = $statement->get_result();
        if (!$result) $statement->close();

        return $result;
    }

    /**
     * Get the parameter type.
     * @param mixed $param Parameter.
     * @return string Parameter type string.
     */
    private function getParamTypes($param): string
    {
        if (!is_array($param)) {
            if (is_string($param))
                return 's';
            elseif (is_int($param))
                return 'i';
            elseif (is_double($param) || is_float($param))
                return 'd';
            else
                return 'b';
        } else {
            $result = '';

            foreach ($param as $value)
                $result .= self::getParamTypes($value);

            return $result;
        }
    }

    public function __destruct()
    {
        $this->connection->close();
    }
}

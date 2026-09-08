<?php
class Database
{
    private $host;
    private $user;
    private $pass;
    private $dbname;
    private $dbh;
    private $stmt;

    public function __construct()
    {
        $this->host = DBHOST;
        $this->user = DBUSER;
        $this->pass = DBPASS;
        $this->dbname = DBNAME;

        try {
            $this->dbh = new PDO(
                'mysql:host=' . $this->host . ';dbname=' . $this->dbname,
                $this->user,
                $this->pass,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
                ]
            );
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    public function exists()
    {
        return $this->stmt->rowCount() > 0;
    }

    public function fetchColumn()
    {
        return $this->stmt->fetchColumn();
    }

    public function query($sql, $params = [])
    {
        $this->stmt = $this->dbh->prepare($sql);

        if (!empty($params)) {
            $param_count = count($params);
            $question_marks = substr_count($sql, '?');

            if ($param_count !== $question_marks) {
                throw new Exception("Parameter mismatch");
            }

            foreach ($params as $index => $value) {
                $this->stmt->bindValue($index + 1, $value);
            }
        }

        $this->stmt->execute();

        return $this->stmt;   // ✅ IMPORTANT FIX
    }

    // --- Addes for profit report ---
    public function resultSet()
    {
        return $this->stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function single()
    {
        return $this->stmt->fetch(PDO::FETCH_OBJ);
    }

    public function fetch()
    {
        return $this->stmt->fetch(PDO::FETCH_OBJ);
    }

    public function fetchAll()
    {
        return $this->stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function rowCount()
    {
        return $this->stmt->rowCount();
    }

    public function lastInsertId()
    {
        return $this->dbh ? $this->dbh->lastInsertId() : null;
    }

// ------- Transaction Section -------

   public function beginTransaction()
{
    return $this->dbh->beginTransaction();
}

public function commit()
{
    return $this->dbh->commit();
}

public function rollBack()
{
    return $this->dbh->rollBack();
}

public function inTransaction()
{
    return $this->dbh->inTransaction();
}

public function transaction(callable $callback)
{
    $this->beginTransaction();

    try {

        $result = $callback($this);

        $this->commit();

        return $result;

    } catch (Throwable $e) {

        if ($this->inTransaction()) {
            $this->rollBack();
        }

        throw $e;
    }
}

// ------ Transaction Section End -------


    
    public function bind($param, $value, $type = null)
    {
        if ($type === null) {

            switch (true) {

                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;

                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;

                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;

                default:
                    $type = PDO::PARAM_STR;
            }
        }

        $this->stmt->bindValue($param, $value, $type);
    }
}

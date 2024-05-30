<?php
class Database {
    private $host;
    private $dbName;
    private $username;
    private $password;
    private $charset;
    private static $pdo = null;
    private $error;
    private $stmt;

    private static $instance = null;

    private function __construct() {
        $config = require 'config.php';
        $this->host = $config['host'];
        $this->dbName = $config['dbname'];
        $this->username = $config['username'];
        $this->password = $config['password'];
        $this->charset = $config['charset'];

        $dsn = "mysql:host={$this->host};dbname={$this->dbName};charset={$this->charset}";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        try {
            if (self::$pdo === null) {
                self::$pdo = new PDO($dsn, $this->username, $this->password, $options);
            }
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            echo "Bağlantı hatası: " . $this->error;
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // Sorgu hazırlama
    public function query($sql) {
        $this->stmt = self::$pdo->prepare($sql);
    }

    // Değerleri bağlama
    public function bind($param, $value, $type = null) {
        if (is_null($type)) {
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

    // Sorguyu çalıştırma
    public function execute() {
        return $this->stmt->execute();
    }

    // Sonuçları dizi olarak alma
    public function resultSet() {
        $this->execute();
        return $this->stmt->fetchAll();
    }

    // Tek bir sonucu alma
    public function single() {
        $this->execute();
        return $this->stmt->fetch();
    }

    // Etkilenen satır sayısını alma
    public function rowCount() {
        return $this->stmt->rowCount();
    }

    // Tüm kayıtların sayısını alma (sayfalama için)
    public function rowCountTotal($table, $conditions = []) {
        $sql = "SELECT COUNT(*) as total FROM {$table}";
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", array_map(function ($key) {
                return "{$key} = :{$key}";
            }, array_keys($conditions)));
        }
        $this->query($sql);
        foreach ($conditions as $key => $value) {
            $this->bind(":{$key}", $value);
        }
        $this->execute();
        $result = $this->stmt->fetch();
        return $result['total'];
    }

    // Son eklenen ID'yi alma
    public function lastInsertId() {
        return self::$pdo->lastInsertId();
    }

    // İşlem başlatma
    public function beginTransaction() {
        return self::$pdo->beginTransaction();
    }

    // İşlemi geri alma
    public function rollBack() {
        return self::$pdo->rollBack();
    }

    // İşlemi onaylama
    public function commit() {
        return self::$pdo->commit();
    }

    // CREATE (Veri ekleme)
    public function create($table, $data) {
        $keys = array_keys($data);
        $fields = implode(", ", $keys);
        $placeholders = ":" . implode(", :", $keys);
        $sql = "INSERT INTO {$table} ({$fields}) VALUES ({$placeholders})";
        $this->query($sql);
        foreach ($data as $key => $value) {
            $this->bind(":{$key}", $value);
        }
        return $this->execute();
    }

    // READ (Veri okuma)
    public function read($table, $conditions = [], $fields = "*") {
        $sql = "SELECT {$fields} FROM {$table}";
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", array_map(function ($key) {
                return "{$key} = :{$key}";
            }, array_keys($conditions)));
        }
        $this->query($sql);
        foreach ($conditions as $key => $value) {
            $this->bind(":{$key}", $value);
        }
        return $this->resultSet();
    }

    // UPDATE (Veri güncelleme)
    public function update($table, $data, $conditions) {
        $fields = implode(", ", array_map(function ($key) {
            return "{$key} = :{$key}";
        }, array_keys($data)));
        $conditionFields = implode(" AND ", array_map(function ($key) {
            return "{$key} = :condition_{$key}";
        }, array_keys($conditions)));
        $sql = "UPDATE {$table} SET {$fields} WHERE {$conditionFields}";
        $this->query($sql);
        foreach ($data as $key => $value) {
            $this->bind(":{$key}", $value);
        }
        foreach ($conditions as $key => $value) {
            $this->bind(":condition_{$key}", $value);
        }
        return $this->execute();
    }

    // DELETE (Veri silme)
    public function delete($table, $conditions) {
        $conditionFields = implode(" AND ", array_map(function ($key) {
            return "{$key} = :{$key}";
        }, array_keys($conditions)));
        $sql = "DELETE FROM {$table} WHERE {$conditionFields}";
        $this->query($sql);
        foreach ($conditions as $key => $value) {
            $this->bind(":{$key}", $value);
        }
        return $this->execute();
    }

    // SQL enjeksiyonlarını önlemek için verileri temizleme
    public function sanitize($data) {
        return htmlspecialchars(strip_tags($data));
    }

    // Parola şifreleme
    public function hashPassword($password) {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    // Parola doğrulama
    public function verifyPassword($password, $hashedPassword) {
        return password_verify($password, $hashedPassword);
    }

    // Sayfalama
    public function paginate($table, $page, $perPage, $conditions = [], $fields = "*") {
        $offset = ($page - 1) * $perPage;
        $sql = "SELECT {$fields} FROM {$table}";
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", array_map(function ($key) {
                return "{$key} = :{$key}";
            }, array_keys($conditions)));
        }
        $sql .= " LIMIT :limit OFFSET :offset";
        $this->query($sql);
        foreach ($conditions as $key => $value) {
            $this->bind(":{$key}", $value);
        }
        $this->bind(':limit', $perPage, PDO::PARAM_INT);
        $this->bind(':offset', $offset, PDO::PARAM_INT);
        return $this->resultSet();
    }
}

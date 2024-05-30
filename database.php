<?php
class Database {
    private $host = 'localhost'; // Veritabanı sunucusu
    private $dbName = 'veritabani_adi'; // Veritabanı adı
    private $username = 'kullanici_adi'; // Veritabanı kullanıcı adı
    private $password = 'sifre'; // Veritabanı şifresi
    private $charset = 'utf8mb4'; // Karakter seti
    private $pdo;
    private $error;
    private $stmt;

    public function __construct() {
        // PDO DSN (Data Source Name) oluşturma
        $dsn = "mysql:host={$this->host};dbname={$this->dbName};charset={$this->charset}";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        try {
            // PDO instance oluşturma
            $this->pdo = new PDO($dsn, $this->username, $this->password, $options);
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            echo "Bağlantı hatası: " . $this->error;
        }
    }

    // Sorgu hazırlama
    public function query($sql) {
        $this->stmt = $this->pdo->prepare($sql);
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

    // Son eklenen ID'yi alma
    public function lastInsertId() {
        return $this->pdo->lastInsertId();
    }

    // İşlem başlatma
    public function beginTransaction() {
        return $this->pdo->beginTransaction();
    }

    // İşlemi geri alma
    public function rollBack() {
        return $this->pdo->rollBack();
    }

    // İşlemi onaylama
    public function commit() {
        return $this->pdo->commit();
    }
}

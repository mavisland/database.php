# database.php

PHP ile veritabanı işlemlerini kolaylaştırmak için oluşturulmuş bir PDO sınıfı. Bu sınıf, veritabanı bağlantılarını yönetir ve CRUD (Create, Read, Update, Delete) işlemlerini basit hale getirir. Ayrıca, güvenlik önlemleri ve sayfalama (pagination) işlemleri de içerir.

## Kurulum

1. `Database.php` dosyasını projenize ekleyin.
2. Veritabanı bağlantı bilgilerinizi içeren bir `config.php` dosyası oluşturun.

`config.php` içeriği:
```php
<?php
return [
    'host' => 'localhost',
    'dbname' => 'your_database_name',
    'username' => 'your_username',
    'password' => 'your_password',
    'charset' => 'utf8'
];
```

## Kullanım

### Veritabanı Bağlantısı Alma

```php
require_once 'Database.php';

// Veritabanı bağlantısı alma
$db = Database::getInstance();
```

### Fonksiyonlar ve Kullanım Örnekleri

#### Veri Ekleme (CREATE)

```php
$data = [
    'name' => $db->sanitize('John Doe'),
    'email' => $db->sanitize('john@example.com'),
    'password' => $db->hashPassword('password123')
];
$db->create('users', $data);
```

#### Veri Okuma (READ)

```php
$conditions = ['email' => $db->sanitize('john@example.com')];
$user = $db->read('users', $conditions);
print_r($user);
```

#### Veri Güncelleme (UPDATE)

```php
$data = ['name' => $db->sanitize('Jane Doe')];
$conditions = ['email' => $db->sanitize('john@example.com')];
$db->update('users', $data, $conditions);
```

#### Veri Silme (DELETE)

```php
$conditions = ['email' => $db->sanitize('john@example.com')];
$db->delete('users', $conditions);
```

#### Parola Doğrulama

```php
$conditions = ['email' => $db->sanitize('john@example.com')];
$user = $db->read('users', $conditions);

if ($db->verifyPassword('password123', $user[0]['password'])) {
    echo "Parola doğru!";
} else {
    echo "Parola yanlış!";
}
```

#### Sayfalama (Paginate)

```php
$page = 1; // Şu anki sayfa
$perPage = 10; // Sayfa başına kayıt sayısı
$users = $db->paginate('users', $page, $perPage);
print_r($users);

// Toplam kayıt sayısını almak için
$totalRecords = $db->rowCountTotal('users');
echo "Toplam kayıt sayısı: " . $totalRecords;
```

## Fonksiyonlar

### `getInstance()`

Singleton tasarım deseni kullanarak, sınıfın tek bir örneğini döner.

### `query($sql)`

Bir SQL sorgusu hazırlar.

### `bind($param, $value, $type = null)`

Bir değeri belirtilen parametreye bağlar. Veri tipini otomatik olarak belirler.

### `execute()`

Hazırlanan sorguyu çalıştırır.

### `resultSet()`

Çalıştırılan sorgunun tüm sonuçlarını döner.

### `single()`

Çalıştırılan sorgunun tek bir sonucunu döner.

### `rowCount()`

Çalıştırılan sorgudan etkilenen satır sayısını döner.

### `rowCountTotal($table, $conditions = [])`

Belirtilen tablo ve koşullara göre toplam kayıt sayısını döner.

### `lastInsertId()`

Son eklenen kaydın ID'sini döner.

### `beginTransaction()`

Bir veritabanı işlemini başlatır.

### `rollBack()`

Başlatılan veritabanı işlemini geri alır.

### `commit()`

Başlatılan veritabanı işlemini onaylar.

### `create($table, $data)`

Belirtilen tabloya yeni bir kayıt ekler.

### `read($table, $conditions = [], $fields = "*")`

Belirtilen tablo ve koşullara göre kayıtları okur.

### `update($table, $data, $conditions)`

Belirtilen tablo ve koşullara göre kayıtları günceller.

### `delete($table, $conditions)`

Belirtilen tablo ve koşullara göre kayıtları siler.

### `sanitize($data)`

Veriyi SQL enjeksiyonlarından ve diğer güvenlik tehditlerinden korumak için temizler.

### `hashPassword($password)`

Parolayı şifreler (hash).

### `verifyPassword($password, $hashedPassword)`

Parolayı doğrular.

### `paginate($table, $page, $perPage, $conditions = [], $fields = "*")`

Belirtilen tablo ve koşullara göre sayfalama yaparak kayıtları döner.

## Lisans

Bu proje MIT lisansı ile lisanslanmıştır. Daha fazla bilgi için LICENSE dosyasına bakın.

Bu `README.md` dosyası, `Database` sınıfının kurulumunu, fonksiyonlarının kısa açıklamalarını ve kullanım örneklerini içerir. Bu sayede, bu sınıfı projelerinizde rahatlıkla kullanabilirsiniz.

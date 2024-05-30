# database.php

PHP'de veritabanı işlemlerini gerçekleştirebileceğiniz ve sorgu yapabileceğiniz bir PDO sınıfı oluşturmak, veritabanı işlemlerini daha güvenli ve daha kolay yönetilebilir hale getirir. İşte basit bir PDO sınıfı örneği:

Bu sınıfı kullanarak veritabanı işlemlerini daha basit ve güvenli bir şekilde gerçekleştirebilirsiniz. İşte birkaç kullanım örneği:

## Kullanım Örnekleri

1. **Veritabanına Bağlanma ve Veri Ekleme**

```php
<?php
require_once 'Database.php';

$db = new Database();

$db->query("INSERT INTO users (name, email) VALUES (:name, :email)");
$db->bind(':name', 'John Doe');
$db->bind(':email', 'john@example.com');
$db->execute();
```

2. **Veri Seçme**

```php
$db = new Database();
$db->query("SELECT * FROM users WHERE email = :email");
$db->bind(':email', 'john@example.com');
$user = $db->single();

print_r($user);
```

3. **Veri Güncelleme**

```php
$db->query("UPDATE users SET name = :name WHERE email = :email");
$db->bind(':name', 'Jane Doe');
$db->bind(':email', 'john@example.com');
$db->execute();
```

4. **Veri Silme**

```php
$db = new Database();
$db->query("DELETE FROM users WHERE email = :email");
$db->bind(':email', 'john@example.com');
$db->execute();
```

Bu örnekler PDO sınıfının temel kullanımını göstermektedir. Daha karmaşık işlemler için sınıfı genişletebilirsiniz.

## CRUD Kullanım Örnekleri

CRUD (Create, Read, Update, Delete) işlemleri için fonksiyonlar ekleyerek PDO sınıfını genişletebiliriz. Bu fonksiyonlar, veritabanı işlemlerini daha modüler ve okunabilir hale getirecektir.

### Veri Ekleme (CREATE) ve Parola Şifreleme

```php
$db = new Database();
$data = [
    'name' => $db->sanitize('John Doe'),
    'email' => $db->sanitize('john@example.com'),
    'password' => $db->hashPassword('password123')
];
$db->create('users', $data);
```

### Veri Okuma (READ)

```php
$db = new Database();
$conditions = ['email' => $db->sanitize('john@example.com')];
$user = $db->read('users', $conditions);
print_r($user);
```

### Veri Güncelleme (UPDATE)

```php
$db = new Database();
$data = ['name' => $db->sanitize('Jane Doe')];
$conditions = ['email' => $db->sanitize('john@example.com')];
$db->update('users', $data, $conditions);
```

### Veri Silme (DELETE)

```php
$db = new Database();
$conditions = ['email' => $db->sanitize('john@example.com')];
$db->delete('users', $conditions);
```

### Parola Doğrulama

```php
$db = new Database();
$conditions = ['email' => $db->sanitize('john@example.com')];
$user = $db->read('users', $conditions);

if ($db->verifyPassword('password123', $user[0]['password'])) {
    echo "Parola doğru!";
} else {
    echo "Parola yanlış!";
}
```

### Sayfalama (Paginate)

Bu yapıyla, sayfalama işlemlerini kolayca gerçekleştirebilirsiniz. `paginate` fonksiyonu, belirli bir sayfa numarası ve sayfa başına kayıt sayısına göre veri çekmenizi sağlar. `rowCountTotal` fonksiyonu ise, toplam kayıt sayısını hesaplamanızı sağlar. Bu fonksiyonları kullanarak, sayfalama işlemlerini rahatlıkla yönetebilirsiniz.

```php
$db = new Database();
$page = 1; // Şu anki sayfa
$perPage = 10; // Sayfa başına kayıt sayısı
$users = $db->paginate('users', $page, $perPage);
print_r($users);

// Toplam kayıt sayısını almak için
$totalRecords = $db->rowCountTotal('users');
echo "Toplam kayıt sayısı: " . $totalRecords;
```

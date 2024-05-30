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
<?php
require_once 'Database.php';

$db = new Database();

$db->query("SELECT * FROM users WHERE email = :email");
$db->bind(':email', 'john@example.com');
$user = $db->single();

print_r($user);
```

3. **Veri Güncelleme**

```php
<?php
require_once 'Database.php';

$db = new Database();

$db->query("UPDATE users SET name = :name WHERE email = :email");
$db->bind(':name', 'Jane Doe');
$db->bind(':email', 'john@example.com');
$db->execute();
```

4. **Veri Silme**

```php
<?php
require_once 'Database.php';

$db = new Database();

$db->query("DELETE FROM users WHERE email = :email");
$db->bind(':email', 'john@example.com');
$db->execute();
```

Bu örnekler PDO sınıfının temel kullanımını göstermektedir. Daha karmaşık işlemler için sınıfı genişletebilirsiniz.

## CRUD Kullanım Örnekleri

CRUD (Create, Read, Update, Delete) işlemleri için fonksiyonlar ekleyerek PDO sınıfını genişletebiliriz. Bu fonksiyonlar, veritabanı işlemlerini daha modüler ve okunabilir hale getirecektir.

### Veri Ekleme (CREATE)

```php
<?php
require_once 'Database.php';

$db = new Database();
$data = [
    'name' => 'John Doe',
    'email' => 'john@example.com'
];
$db->create('users', $data);
```

### Veri Okuma (READ)

```php
<?php
require_once 'Database.php';

$db = new Database();
$conditions = ['email' => 'john@example.com'];
$user = $db->read('users', $conditions);
print_r($user);
```

### Veri Güncelleme (UPDATE)

```php
<?php
require_once 'Database.php';

$db = new Database();
$data = ['name' => 'Jane Doe'];
$conditions = ['email' => 'john@example.com'];
$db->update('users', $data, $conditions);
```

### Veri Silme (DELETE)

```php
<?php
require_once 'Database.php';

$db = new Database();
$conditions = ['email' => 'john@example.com'];
$db->delete('users', $conditions);
```




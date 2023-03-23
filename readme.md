# PratikDB

PratikDB, PHP programlama dilinin en güçlü veritabanı bağlantı aracı olan PDO kullanarak MySQL sorgularını oluşturmak ve çalıştırmak için özel olarak tasarlanmıştır. Esnek yapısı ve basit kullanımı sayesinde, geliştiricilerin işlerini kolaylaştırır ve zaman kazandırır.

PratikDB, güvenlik ve optimize edilmiş sorgular sağlamak için bağlamalı parametreler ve dinamik bir yapı kullanır. Bu sayede, veritabanına gönderilen sorguların güvenliği sağlanır ve daha iyi performans elde edilir.

Sınıfın tasarımı, popüler PHP framework'ü Laravel'den ilham alınarak yapılmıştır. Bu nedenle, Laravel kullanıcılarına benzer bir deneyim sunar.

PratikDB, geliştiricilerin veritabanı işlemlerini hızlandırmalarına, kodlarını daha düzenli hale getirmelerine ve daha az hata yapmalarına yardımcı olur. Bu nedenle, birçok PHP projesinde tercih edilen bir sınıftır.

### Gereksinimler

- PHP 7.1 ve üzeri
- MySQL veritabanı

### Kurulum

1. Projeyi kendi projenizde kullanmak için `PratikDB.php` dosyasını projenizin ilgili klasörüne kopyalayın.
2. `db.php` dosyasını kendi veritabanı bilgilerinizle güncelleyin.
3. `PratikDB.php` dosyasında, `PratikDB` sınıfını ve `dd()` yardımcı fonksiyonunu kullanabilmek için dosyayı projenizin diğer PHP dosyalarına dahil edin.

```php
include 'config/db.php'; 
include 'config/pratikdb.php'; 
```

#### Kullanım
PratikDB sınıfı, aşağıdaki gibi temel CRUD işlemleri ve daha fazlasını gerçekleştirebilir.

```php
//Veritabanı bağlantısı oluşturma 
$database = new Database(); 
$db = $database->getConnection(); 
$pratikdb = new PratikDB($db); 
  
//Veri ekleme
$insertedId = $pratikdb->table('products')
    ->create([
        'name' => 'Ürün 1',
        'price' => 100,
    ]);

//Veri güncelleme
$affectedRows = $pratikdb->table('products')
    ->where('id', 1)
    ->update([
        'name' => 'Ürün 1 Yeni',
        'price' => 200,
    ]);

//Veri silme
$affectedRows = $pratikdb->table('products')
    ->where('id', 1)
    ->delete();

//Veri okuma ve filtreleme
$results = $pratikdb->table('products')
    ->select(['name', 'price'])
    ->where(function ($query) {
        $query->where('price', '>', 50);
        $query->orWhere('name', 'LIKE', '%Product%');
    })
    ->orderBy('price', 'DESC')
    ->limit(10)
    ->get();

//İlişkili tablolarla çalışma 
$results = $pratikdb->table('products')
    ->select(['products.name', 'categories.name AS category_name'])
    ->join('categories', 'products.category_id', 'categories.id')
    ->get();
```

### Diğer özellikler

PratikDB sınıfı, `where`, `whereIn`, `whereBetween`, `when` gibi filtreleme ve koşul sağlayan daha fazla yönteme sahiptir. Ayrıca `pluck`, `toArray`, `toJson` ve `toSql` gibi ek yardımcı işlevler sağlar. Bu yöntemlerin daha fazla örneği şunlardır:

#### Pluck

Sorgu sonucunda belirtilen sütundaki değerleri alın:

```php
$names = $pratikdb->table('products')->pluck('name');
 ```

#### To Array
Sonuçları, anahtar-değer çiftleri olarak içeren bir diziye dönüştürün:

```php
$resultsArray = $pratikdb->table('products')->toArray();
 ```
 
#### To JSON
Sonuçları JSON formatında döndürün:
```php
$resultsJson = $pratikdb->table('products')->toJson();
 ```

#### To SQL
Sorguyu oluşturun ve sorgu dizesini görüntüleyin (veritabanında çalıştırmadan):

```php
$pratikdb->table('products')
    ->select(['name', 'price'])
    ->where('price', '>', 50)
    ->toSql();
 ```
 
# PratikDB QueryBuilder

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

### Kullanım - Aynı Sayfada Birden Fazla Sorgu

<b>Eğer aynı sayfada birden fazla sorgu çalıştırmak isterseniz, her sorgu için yeni bir PratikDB nesnesi oluşturarak önceki sorguların birbirini etkilemesini önleyebilirsiniz.</b>
PratikDB sınıfı, aşağıdaki gibi temel CRUD işlemleri ve daha fazlasını gerçekleştirebilir.

```php
//örnek kullanımlar
$pratikdb1 = new PratikDB($db);
$deneme1 = $pratikdb1->table('products')
    ->whereIn('id', [2, 3, 4])
    ->get();

$pratikdb2 = new PratikDB($db);
$names = $pratikdb2->table('products')->pluck('name'); 
```

```php
//Veritabanı bağlantısı oluşturma 
$database = new Database(); 
$db = $database->getConnection();  
$pratikdb = new PratikDB($db); 

/*
PostgreSQL kullanmak isterseniz
$database = new Database("postgresql");
$db = $database->getConnection();
*/
  
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

#### whereIn

whereIn fonksiyonu, belirtilen sütunda değerlerin bir dizi ile eşleşip eşleşmediğini sorgulamak için kullanılır.

```php
$result = $pratikdb->table('users')
             ->whereIn('id', [1, 2, 3])
             ->get();
 ```

 #### whereBetween

whereBetween fonksiyonu, belirtilen sütunda verilen iki değer arasında kalan değerleri sorgulamak için kullanılır.

```php
$result = $pratikdb->table('users')
             ->whereBetween('age', 18, 30)
             ->get();
 ```

  #### when

when fonksiyonu, belirtilen koşulun doğru olması durumunda bir işlem yapmak için kullanılır. Koşul yanlışsa, varsayılan bir işlem yapılır.

```php

$result = $pratikdb->table('users')
             ->when($isAdult, function($query) {
                 $query->where('age', '>=', 18);
             })
 ```



  #### with

with fonksiyonu, sınıfta kullanılan bir yöntemdir ve CTE (Common Table Expressions) denilen yapıları desteklemeye yarar. CTE'ler, karmaşık SQL sorgularını daha okunabilir ve düzenli hale getirmek için kullanılabilir.

Kısaca, with fonksiyonu sayesinde, bir alt sorguyu (subquery) temsil eden CTE'yi tanımlayabilir ve ana sorgunuzda bu CTE'yi kullanarak daha karmaşık sorguları daha basit ve anlaşılır hale getirebilirsiniz.

```php 
$result = $pratikdb->with('total_scores', function ($query) {
    $query->table('students')
        ->select('students.id', 'first_name', 'last_name', 'score')
        ->join('scores', 'students.id', '=', 'scores.student_id');
})
->table('total_scores')
->groupBy('id', 'first_name', 'last_name')
->select('first_name', 'last_name', 'SUM(score) as total_score')
->get();
 ```



  #### cte
 CTE (Common Table Expressions) fonksiyonu, SQL sorgularında kullanılabilen geçici sonuç kümesi oluşturmanıza olanak tanır. Bu sınıfın cte fonksiyonu, veritabanı sorgularında CTE kullanmayı kolaylaştırmak amacıyla oluşturulmuştur.

Fonksiyon, bir takma ad (alias) ve geri çağırılabilir (callable) bir işlev (callback) alır. İşlev, alt sorgunun nasıl oluşturulacağını tanımlar. cte fonksiyonu, önce alt sorguyu oluşturur ve ardından bağlamaları (bindings) birleştirir. Daha sonra, ana tabloyu CTE'ye göre günceller ve CTE'yi temel alarak veritabanı sorgularını gerçekleştirmenize olanak sağlar.

Özetle, cte fonksiyonu, CTE'leri kullanarak daha karmaşık sorgular yapmanıza ve kodunuzu daha okunabilir hale getirmenize yardımcı olur.

```php
$result = $pratikdb->cte('max_scores', function ($subQuery) {
    $subQuery->table('top_scores')
             ->select('user_id', 'MAX(score) as max_score')
             ->groupBy('user_id');
})->select('user_id', 'max_score')
  ->table('top_scores')
  ->join('max_scores', 'top_scores.user_id', '=', 'max_scores.user_id')
  ->whereRaw('top_scores.score = max_scores.max_score')
  ->get();
```

 #### GROUP BY 
 GROUP BY SQL ifadesi, belirtilen sütunlara göre benzer kayıtları gruplamak ve her bir grup üzerinde toplu işlemler gerçekleştirmek için kullanılır. Bu işlemler, toplama, sayma, ortalama alma gibi toplu işlemleri içerebilir.
 ```php
 $data = $pratikdb->table('categories')->groupBy('cat_name')->get();
```

#### Pluck

Sorgu sonucunda belirtilen sütundaki değerleri alın:

```php
$names = $pratikdb->table('products')->pluck('name');
$names = $pratikdb->table('products')->pluck('name','id')->toArray();
$names = $pratikdb->table('products')->pluck('name','id')->toJson();
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
 
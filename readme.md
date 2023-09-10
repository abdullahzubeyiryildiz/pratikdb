# PratikDB QueryBuilder

PratikDB is specifically designed to create and execute MySQL queries using PDO, the most powerful database connection tool in the PHP programming language. Its flexible structure and easy-to-use interface make it a valuable tool for developers, simplifying their work and saving them time.

PratikDB utilizes parameter binding and a dynamic structure to ensure security and optimize queries sent to the database. This ensures the security of the queries sent to the database and results in better performance.

The design of the class is inspired by the popular PHP framework Laravel, offering a similar experience to Laravel users.

PratikDB helps developers speed up their database operations, maintain cleaner code, and make fewer errors. Therefore, it's a preferred class in many PHP projects.

### Requirements

- PHP 7.1 and above
- MySQL database

### Installation

1. To use the PratikDB in your project, copy the `PratikDB.php` file to the relevant folder in your project.
2. Update the `db.php` file with your database credentials.
3. Include the `PratikDB.php` file in other PHP files of your project to use the `PratikDB` class and the`dd()` helper function.

```php
include 'config/db.php'; 
include 'config/pratikdb.php'; 
```

### Usage - Running Multiple Queries on the Same Page

<b>If you want to execute multiple queries on the same page, you can prevent previous queries from affecting each other by creating a new PratikDB object for each query.</b> The PratikDB class can perform basic CRUD operations and more, as demonstrated below:

```php
// Example usages
$pratikdb1 = new PratikDB($db);
$deneme1 = $pratikdb1->table('products')
    ->whereIn('id', [2, 3, 4])
    ->get();

$pratikdb2 = new PratikDB($db);
$names = $pratikdb2->table('products')->pluck('name'); 
```

```php
//Creating a database connection
$database = new Database(); 
$db = $database->getConnection();  
$pratikdb = new PratikDB($db); 

/*
If you want to use PostgreSQL:
$database = new Database("postgresql");
$db = $database->getConnection();
*/
  
//Data insertion
$insertedId = $pratikdb->table('products')
    ->create([
        'name' => 'Product 1',
        'price' => 100,
    ]);

//Data update
$affectedRows = $pratikdb->table('products')
    ->where('id', 1)
    ->update([
        'name' => 'Product 1 New',
        'price' => 200,
    ]);

//Data deletion
$affectedRows = $pratikdb->table('products')
    ->where('id', 1)
    ->delete();

//Data retrieval and filtering
$results = $pratikdb->table('products')
    ->select(['name', 'price'])
    ->where(function ($query) {
        $query->where('price', '>', 50);
        $query->orWhere('name', 'LIKE', '%Product%');
    })
    ->orderBy('price', 'DESC')
    ->limit(10)
    ->get();

//Working with related tables
$results = $pratikdb->table('products')
    ->select(['products.name', 'categories.name AS category_name'])
    ->join('categories', 'products.category_id', 'categories.id')
    ->get();
```

### Other Features

The PratikDB class offers more methods for filtering and conditionally querying data, such as `where`, `whereIn`, `whereBetween` `when`. It also provides additional helper functions like `pluck`, `toArray`, `toJson` and `toSql`. Here are more examples of these methods:

#### whereIn

The whereIn function is used to query whether values in a specified column match an array of values.

```php
$result = $pratikdb->table('users')
             ->whereIn('id', [1, 2, 3])
             ->get();
 ```

 #### whereBetween

The whereBetween function is used to query values in a specified column that fall between two given values.

```php
$result = $pratikdb->table('users')
             ->whereBetween('age', 18, 30)
             ->get();
 ```

  #### when

The when function is used to perform an operation based on a specified condition. If the condition is false, a default operation is performed.

```php

$result = $pratikdb->table('users')
             ->when($isAdult, function($query) {
                 $query->where('age', '>=', 18);
             })
 ```



  #### with

The with function is a method used within the class and is intended to support Common Table Expressions (CTEs). CTEs can be used to make complex SQL queries more readable and organized.

In short, the with function allows you to define a CTE representing a subquery and then use this CTE in your main query to simplify and clarify more complex queries.

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
The cte function is used for creating Common Table Expressions (CTEs) in SQL queries. CTEs allow you to create temporary result sets in SQL queries. This function takes an alias and a callable function that defines how the subquery is constructed.

The cte function creates the subquery, combines bindings, updates the main table based on the CTE, and allows you to perform database queries based on the CTE.

In summary, the cte function facilitates the use of CTEs to make complex queries and improve code readability.

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
The GROUP BY SQL statement is used to group similar records based on specified columns and perform aggregate functions on each group. These functions can include operations like summing, counting, or averaging.
 ```php
 $data = $pratikdb->table('categories')->groupBy('cat_name')->get();
```

#### Pluck

Retrieve values from a specific column in the query result:

```php
$names = $pratikdb->table('products')->pluck('name');
$names = $pratikdb->table('products')->pluck('name','id')->toArray();
$names = $pratikdb->table('products')->pluck('name','id')->toJson();
 ```

#### To Array
Convert the results into an array with key-value pairs:

```php
$resultsArray = $pratikdb->table('products')->toArray();
 ```
 
#### To JSON
Return the results in JSON format:
```php
$resultsJson = $pratikdb->table('products')->toJson();
 ```

#### To SQL
Build the query and display the SQL query string (without executing it in the database):

```php
$pratikdb->table('products')
    ->select(['name', 'price'])
    ->where('price', '>', 50)
    ->toSql();
 ```
 
<?php
include_once 'config/db.php'; 
include_once 'config/pratikdb.php'; 
 

$database = new Database(); 
$db = $database->getConnection(); 
$pratikdb = new PratikDB($db);


$names = $pratikdb->table('products')->pluck('name');

$result = $names->toArray(); 


$activated = 1;  

$results = $pratikdb->table('products')
        ->where(function ($query) use ($activated) {
            $query->where('status', '=', $activated); 
        })
    ->get();

dd($results);


$products = $query->table('products')
    ->select('products.name', 'products.price', 'categories.name as category')
    ->join('categories', 'products.category_id', '=', 'categories.id')
    ->whereBetween('products.price', 10, 20)
    ->orderBy('categories.name', 'ASC')
    ->get();  


$products = $query->table('products') 
->orderBy('id', 'DESC')
->limit(4)
->toSql();  
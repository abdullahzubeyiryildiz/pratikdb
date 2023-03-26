<?php
include_once 'config/db.php'; 
include_once 'config/pratikdb.php'; 
 

$database = new Database(); 
$db = $database->getConnection(); 
$pratikdb = new PratikDB($db);

/*
$names = $pratikdb->table('products')->pluck('name');

$result = $names->toArray(); 


$activated = 1;  

$results = $pratikdb->table('products')
        ->where(function ($query) use ($activated) {
            $query->where('status', '=', $activated); 
        })
    ->get(); */

 
 // $names = $pratikdb->table('products')->toArray();
 // $names = $pratikdb->table('products')->toJson();
  //  $names = $pratikdb->table('products')->pluck('name', 'id')->toArray();
  $results = $pratikdb->table('products')
    ->select(['name', 'price'])
    ->where(function ($query) {
        $query->where('price', '>', 50);
        $query->orWhere('name', 'LIKE', '%test%');
    })
    ->orderBy('price', 'DESC')
    ->limit(10)
    ->get();

  /*  $products = $pratikdb->table('products')
    ->select('products.name', 'products.price', 'categories.name as category')
    ->join('categories', 'products.category_id', '=', 'categories.id')
    ->whereBetween('products.price', 10, 100, 'AND')
    ->orderBy('products.price', 'DESC')
    ->limit(10)
    ->get();
    */

    $product = $pratikdb->table('products')
    ->select('products.name', 'products.price', 'categories.cat_name as category')
    ->join('categories', 'products.category_id', '=', 'categories.id')
    ->whereBetween('products.price', 10, 100) 
    ->first();
   
   /* $products = $pratikdb->table('products')
    ->select('products.name', 'products.price', 'categories.name as category')
    ->join('categories', 'products.category_id', '=', 'categories.id')
    ->where('price', '>', 10)
    ->where('price', '<', 100, 'OR')
    ->buildQuery(); */
 


    /* 
    ->select('products.name', 'products.price', 'categories.name as category')
    ->join('categories', 'products.category_id', '=', 'categories.id')
    ->where('products.price', '>', 10)
    ->where('products.price', '<', 100)
    ->where('products.name', 'LIKE', '%m%')
    ->orderBy('products.price', 'DESC')*/
dd($product);
//,'id'

$products = $pratikdb->table('products')
    ->select('products.name', 'products.price', 'categories.name as category')
    ->join('categories', 'products.category_id', '=', 'categories.id')
    ->whereBetween('products.price', 10, 20)
    ->orderBy('categories.name', 'ASC')
    ->get();  


$products = $pratikdb->table('products') 
->orderBy('id', 'DESC')
->limit(4)
->toSql();  
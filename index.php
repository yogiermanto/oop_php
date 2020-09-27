<?php

require "Db.php";

use Db\Db;

$Db = Db::getInstance();


// $query = "INSERT INTO barang (nama_barang, jumlah_barang, harga_barang) VALUES (?, ?, ?)";
// $bindValue = [ 'Cosmos CRJ-8229 - Rice Cooker', 4, 299000];
// $result = $Db->runQuery($query, $bindValue);

// $result = $Db->getQuery("SELECT * FROM barang");
$result = $Db->select('nama_barang')
             ->select('jumlah_barang')
             ->orderBy('jumlah_barang', 'DESC')
             ->get('barang', );

var_dump($result);
<?php

require "Db.php";

use Db\Db;

$Db = Db::getInstance();


// $query = "INSERT INTO barang (nama_barang, jumlah_barang, harga_barang) VALUES (?, ?, ?)";
// $bindValue = [ 'Cosmos CRJ-8229 - Rice Cooker', 4, 299000];
// $result = $Db->runQuery($query, $bindValue);

// $result = $Db->getQuery("SELECT * FROM barang");
// $result = $Db->select('id_barang, nama_barang')
//              ->select('jumlah_barang')
//              ->orderBy('jumlah_barang', 'DESC')
//              ->getWhere('barang', ['id_barang', '=', '5']);

// $result = $Db->select('nama_barang, jumlah_barang')
//              ->getLike('barang', 'nama_barang', '%k%');

// if ($Db->check('barang', 'id_barang', '10')) {
//     echo 'ID Barang 10 tersedia';
// }

// $result = $Db->insert('barang', [
//             'nama_barang' => 'Philips Blender',
//             'jumlah_barang' => 11,
//             'harga_barang' =>629000
//          ]);

// if ($result) {
//     echo "Terdapat {$Db->count()} data yang ditambahkan!";
// }

// $tableBarang = $Db->get('barang');

// $result = $Db->update('barang', ['nama_barang' => 'Dummy Project', 'harga_barang' => 99999], ['id_barang', '=', 3]);

// if ($result) {
//     echo "Terdapat {$Db->count()} data yang diubah";
// }

$result = $Db->delete('barang', ['id_barang', '<', 5]);

if ($result)
{
    echo "Terdapat {$Db->count()} data yang diubah";
}
// echo $result->id_barang;
// var_dump($tableBarang);
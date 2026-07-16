<?php
session_start();      // mengaktifkan session

// pengecekan session login user 
// jika user belum login
if (empty($_SESSION['username']) && empty($_SESSION['password'])) {
  // alihkan ke halaman login dan tampilkan pesan peringatan login
  header('location: ../../login.php?pesan=2');
}
// jika user sudah login, maka jalankan perintah untuk insert
else {
  // panggil file "database.php" untuk koneksi ke database
  require_once "../../config/database.php";

  // mengecek data hasil submit dari form
  if (isset($_POST['simpan'])) {
    // ambil data hasil submit dari form
    $id_transaksi  = mysqli_real_escape_string($mysqli, $_POST['id_transaksi']);
    $tanggal       = mysqli_real_escape_string($mysqli, trim($_POST['tanggal']));
    $barang_list   = $_POST['barang'];
    $jumlah_list   = $_POST['jumlah'];

    // ubah format tanggal menjadi Tahun-Bulan-Hari (Y-m-d) sebelum disimpan ke database
    $tanggal_masuk = date('Y-m-d', strtotime($tanggal));

    // insert setiap baris item
    $insert_success = true;
    if (!empty($barang_list) && is_array($barang_list)) {
      foreach ($barang_list as $index => $barang) {
        $barang = mysqli_real_escape_string($mysqli, $barang);
        $jumlah = mysqli_real_escape_string($mysqli, $jumlah_list[$index]);

        if ($barang == '' || $jumlah == '' || !is_numeric($jumlah) || $jumlah <= 0) {
          continue;
        }

        $insert = mysqli_query($mysqli, "INSERT INTO tbl_barang_masuk(id_transaksi, tanggal, barang, jumlah) 
                                         VALUES('$id_transaksi', '$tanggal_masuk', '$barang', '$jumlah')")
                                         or die('Ada kesalahan pada query insert : ' . mysqli_error($mysqli));
        if (!$insert) {
          $insert_success = false;
          break;
        }
      }
    }

    if ($insert_success) {
      // alihkan ke halaman barang masuk dan tampilkan pesan berhasil simpan data
      header('location: ../../main.php?module=barang_masuk&pesan=1');
    }
  }
}

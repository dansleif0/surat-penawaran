<?php
// Script untuk menghubungkan folder upload ke public_html

// 1. Tentukan lokasi folder ASLI tempat file disimpan
$targetFolder = '/home/mcetrons/laravel_app/storage/app/public';

// 2. Tentukan lokasi shortcut yang ingin dibuat di public_html
$linkFolder = '/home/mcetrons/public_html/storage';

// 3. Proses pembuatan link
if (file_exists($linkFolder)) {
    echo "Gagal: Folder/Link 'storage' sudah ada di public_html. Silakan hapus dulu.";
} elseif (!file_exists($targetFolder)) {
    echo "Gagal: Folder target penyimpanan ($targetFolder) tidak ditemukan.";
} else {
    symlink($targetFolder, $linkFolder);
    echo "SUKSES! Jalur penyimpanan gambar sudah diperbaiki.";
}
?>
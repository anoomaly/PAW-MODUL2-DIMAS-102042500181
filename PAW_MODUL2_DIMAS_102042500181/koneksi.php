<?php // Sesuaikan akun, sandi, dan nama database dengan komputer Anda. // 127.0.0.1 memakai port TCP yang sama di Windows dan Mac. 
 $conn = mysqli_connect('127.0.0.1', 'root', '', 'modul2db'); 
 mysqli_set_charset($conn, 'utf8mb4');
<?php
$host = "localhost"; $user = "root"; $pass = ""; $db = "schoolbell";
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die(json_encode(["status"=>"error","msg"=>"Koneksi DB gagal"]));
$conn->set_charset("utf8mb4");
?>
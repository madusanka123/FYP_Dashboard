<?php
$host = 'localhost';
$user = 'root';
$pas = '';
$database = 'shading';

$konek = mysqli_connect($host, $user, $pas, $database);

if (!$konek) {
	echo "Connection fail";
}

<?php
$db = mysqli_connect('localhost', 'root', 'root', 'db');
if (mysqli_connect_errno()) {
    die("помилка підключення до бази даних" . mysqli_connect_errno($db));
}
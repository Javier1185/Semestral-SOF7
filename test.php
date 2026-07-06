<?php

try {
    $pdo = new PDO(
        "mysql:host=localhost;dbname=sistema_contable;charset=utf8mb4",
        "root",
        ""
    );

    echo "Conexión exitosa";

} catch (PDOException $e) {
    echo $e->getMessage();
}
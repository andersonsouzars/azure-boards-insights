<?php

$host = 'abi_mariadb'; // Nome do container MariaDB
$db = 'app_database';
$user = 'app_user';
$password = 'app_password';
$dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    
    echo "Conexão bem-sucedida!";


    // SQL para criar a tabela
    $sql = "
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL UNIQUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ";

    // Executando a query
    $pdo->exec($sql);

    echo "Tabela 'users' criada com sucesso!";
    
} catch (PDOException $e) {
    echo "Erro na conexão: " . $e->getMessage();
}
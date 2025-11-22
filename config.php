<?php
// Arquivo: config.php

// Constantes de Conexão com o Banco de Dados (Altere se necessário)
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');    // Geralmente 'root' no XAMPP/WAMP
define('DB_PASSWORD', '');        // Geralmente vazio no XAMPP/WAMP
define('DB_NAME', 'site_db');     // Nome do banco de dados criado

// Tentativa de conexão usando MySQLi
$conexao = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
 
// Verifica a conexão
if($conexao === false){
    die("ERRO: Não foi possível conectar ao banco de dados. " . $conexao->connect_error);
}
?>
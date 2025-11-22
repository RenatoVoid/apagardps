<?php
// Arquivo: config.php

// 1. FORÇA O FUSO HORÁRIO DO PHP PARA BRASÍLIA
date_default_timezone_set('America/Sao_Paulo');

// Constantes de Conexão
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'site_db');

// Conexão
$conexao = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
 
// Verifica erro na conexão
if($conexao->connect_error){
    die("ERRO: Não foi possível conectar ao banco de dados. " . $conexao->connect_error);
}

// 2. FORÇA O BANCO DE DADOS (MySQL) A USAR O HORÁRIO DO PHP
// Isso garante que o comando NOW() do SQL pegue a hora certa (-03:00)
$conexao->query("SET time_zone = '" . date('P') . "'");
?>
<?php
session_start();
require_once "config.php";
require_once "Security.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $senha = $_POST['senha'] ?? '';
    $confirma = $_POST['confirma_senha'] ?? '';

    if (empty($email) || empty($senha) || $senha !== $confirma) {
        $_SESSION['erro_cadastro'] = "Dados inválidos ou senhas não conferem.";
        header("Location: cadastro.php");
        exit();
    }

    // Verifica duplicidade
    $stmt = $conexao->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    if ($stmt->num_rows > 0) {
        $_SESSION['erro_cadastro'] = "E-mail já cadastrado.";
        header("Location: cadastro.php");
        exit();
    }
    $stmt->close();
    
    // --- A MÁGICA HÍBRIDA ---
    
    // 1. HASH (Para Login - Irreversível)
    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

    // 2. CRIPTOGRAFIA RSA (Para o Relatório PDF - Reversível com a chave)
    $senha_para_relatorio = Security::encrypt($senha);

    // Salva os dois no banco + Data Agora
    $agora = date("Y-m-d H:i:s");
    $sql = "INSERT INTO usuarios (email, senha, senha_backup, data_criacao) VALUES (?, ?, ?, ?)";
    
    if ($insert = $conexao->prepare($sql)) {
        // ssss = 4 strings
        $insert->bind_param("ssss", $email, $senha_hash, $senha_para_relatorio, $agora);
        
        if ($insert->execute()) {
            $_SESSION['sucesso_cadastro'] = "Conta criada com dupla camada de segurança!";
        } else {
            $_SESSION['erro_cadastro'] = "Erro ao registrar.";
        }
        $insert->close();
    }
    
    $conexao->close();
    header("Location: cadastro.php");
    exit();
}
?>
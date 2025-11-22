<?php
session_start();
require_once "config.php"; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];
    $confirma_senha = $_POST['confirma_senha'];

    // 1. Validação Simples
    if (empty($email) || empty($senha) || empty($confirma_senha)) {
        $_SESSION['erro_cadastro'] = "Todos os campos devem ser preenchidos.";
        header("Location: cadastro.php");
        exit();
    }
    
    if ($senha !== $confirma_senha) {
        $_SESSION['erro_cadastro'] = "A senha e a confirmação de senha não coincidem.";
        header("Location: cadastro.php");
        exit();
    }

    // 2. Verifica se o e-mail já existe
    $sql_check = "SELECT id FROM usuarios WHERE email = ?";
    if ($stmt_check = $conexao->prepare($sql_check)) {
        $stmt_check->bind_param("s", $email);
        $stmt_check->execute();
        $stmt_check->store_result();
        
        if ($stmt_check->num_rows >= 1) {
            $_SESSION['erro_cadastro'] = "Este e-mail já está cadastrado.";
            $stmt_check->close();
            header("Location: cadastro.php");
            exit();
        }
        $stmt_check->close();
    }
    
    // 3. Criptografia da Senha (OBRIGATÓRIO para segurança)
    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

    // 4. Inserção do novo usuário
    $sql_insert = "INSERT INTO usuarios (email, senha) VALUES (?, ?)";
    
    if ($stmt_insert = $conexao->prepare($sql_insert)) {
        $stmt_insert->bind_param("ss", $email, $senha_hash);
        
        if ($stmt_insert->execute()) {
            $_SESSION['sucesso_cadastro'] = "Usuário cadastrado com sucesso! Use seu e-mail e senha para prosseguir.";
        } else {
            $_SESSION['erro_cadastro'] = "Ocorreu um erro ao tentar cadastrar o usuário: " . $conexao->error;
        }
        
        $stmt_insert->close();
    }
    
    $conexao->close();
    
    header("Location: cadastro.php");
    exit();
} else {
    header("Location: cadastro.php");
    exit();
}
?>
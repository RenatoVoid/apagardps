<?php
session_start();
require_once "config.php";
require_once "Security.php"; // Inclusão da nova classe

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Sanitização básica
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $senha = $_POST['senha'] ?? '';
    $confirma_senha = $_POST['confirma_senha'] ?? '';

    // 1. Validações
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

    // 2. Verifica duplicidade
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
    
    // 3. CRIPTOGRAFIA (Caminho de Ida)
    // Usamos nossa classe Security ao invés de password_hash
    $senha_segura = Security::encrypt($senha);

    // 4. Inserção no Banco
    $sql_insert = "INSERT INTO usuarios (email, senha) VALUES (?, ?)";
    
    if ($stmt_insert = $conexao->prepare($sql_insert)) {
        $stmt_insert->bind_param("ss", $email, $senha_segura);
        
        if ($stmt_insert->execute()) {
            $_SESSION['sucesso_cadastro'] = "Cadastro realizado! Sua senha foi protegida.";
        } else {
            $_SESSION['erro_cadastro'] = "Erro interno ao cadastrar.";
            // Log silencioso do erro real para o admin do servidor
            error_log("MySQL Error: " . $conexao->error);
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
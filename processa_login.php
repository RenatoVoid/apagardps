<?php
// Arquivo: processa_login.php
// VERSÃO FINAL: Com Admin + Correção de Erros
session_start();
require_once "config.php";
require_once "Security.php";

// Se estiver usando PHPMailer, mantenha os requires aqui
// ...

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $email_digitado = trim($_POST['email']);
    $senha_digitada = trim($_POST['senha']);

    // 1. O PULO DO GATO: Buscamos a coluna 'is_admin' também!
    $sql = "SELECT id, email, senha, is_admin FROM usuarios WHERE email = ?";
    
    if ($stmt = $conexao->prepare($sql)) {
        $stmt->bind_param("s", $email_digitado);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows == 1) {
            // Buscamos o resultado, incluindo o status de admin
            $stmt->bind_result($id, $email_db, $senha_hash_db, $is_admin_db);
            $stmt->fetch();
            
            // 2. A CORREÇÃO DO EDITOR (blindado contra erros)
            if (password_verify($senha_digitada, (string)$senha_hash_db)) {
                
                // --- Login Sucesso ---
                $_SESSION['logado'] = true;
                $_SESSION['user_id'] = $id;
                $_SESSION['email_usuario'] = $email_db;

                // 3. ATIVA O MODO DEUS SE FOR ADMIN
                if ($is_admin_db == 1) {
                    $_SESSION['god_mode'] = true;
                } else {
                    $_SESSION['god_mode'] = false;
                }

                // Redireciona para a dashboard
                header("Location: index.php");
                exit();

            } else {
                $_SESSION['erro_login'] = "Senha incorreta.";
            }
        } else {
            $_SESSION['erro_login'] = "Usuário não encontrado.";
        }
        $stmt->close();
    }
    
    header("Location: login.php");
    exit();
}
?>
<?php
session_start();
require_once "config.php";

// Garante que a Etapa 1 foi concluída
if (!isset($_SESSION['2fa_user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $user_id = $_SESSION['2fa_user_id'];
    $codigo_digitado = trim($_POST['codigo']);
    $agora = date("Y-m-d H:i:s");

    // Busca o código 2FA válido (não expirado) no BD
    $sql = "SELECT codigo FROM tokens_2fa WHERE usuario_id = ? AND expira_em > ?";
    
    if ($stmt = $conexao->prepare($sql)) {
        $stmt->bind_param("is", $user_id, $agora);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows == 1) {
            $stmt->bind_result($codigo_db);
            $stmt->fetch();
            
            // Verifica se o código digitado está correto
            if ($codigo_digitado === $codigo_db) {
                
                // --- SUCESSO: LOGIN FINALIZADO ---
                
                // 1. Limpa o token 2FA
                $conexao->query("DELETE FROM tokens_2fa WHERE usuario_id = $user_id");

                // 2. Define as variáveis de SESSÃO de logado
                $_SESSION['logado'] = true;
                
                // Busca o email do usuário para a página principal
                $email_query = $conexao->query("SELECT email FROM usuarios WHERE id = $user_id");
                $email_user = $email_query->fetch_assoc()['email'];
                $_SESSION['email_usuario'] = $email_user;

                unset($_SESSION['2fa_user_id']); 
                
                // Redireciona para a página principal
                header("Location: index.php");
                exit();
                
            } else {
                $_SESSION['erro_2fa'] = "Código de verificação incorreto.";
            }
        } else {
            $_SESSION['erro_2fa'] = "O código expirou ou é inválido. Tente novamente.";
        }
        $stmt->close();
    }
    
    header("Location: verificar_2fa.php");
    exit();
}
<?php
session_start();
require_once "config.php";

// Garante que a Etapa 1 (Login) foi feita
if (!isset($_SESSION['2fa_user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $user_id = $_SESSION['2fa_user_id'];
    $codigo_digitado = trim($_POST['codigo']);
    $agora = date("Y-m-d H:i:s");

    // Busca o código 2FA válido no banco
    $sql = "SELECT codigo FROM tokens_2fa WHERE usuario_id = ? AND expira_em > ?";
    
    if ($stmt = $conexao->prepare($sql)) {
        $stmt->bind_param("is", $user_id, $agora);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows == 1) {
            $stmt->bind_result($codigo_db);
            $stmt->fetch();
            
            // Verifica se o código bate
            if ($codigo_digitado === $codigo_db) {
                
                // --- SUCESSO: LOGIN FINALIZADO ---
                
                // 1. Remove o token 2FA usado (segurança)
                $conexao->query("DELETE FROM tokens_2fa WHERE usuario_id = $user_id");

                // 2. SALVAR DISPOSITIVO COMO CONFIÁVEL (7 DIAS)
                // Gera um token aleatório seguro
                try {
                    $token_device = bin2hex(random_bytes(32));
                } catch (Exception $e) {
                    // Fallback caso random_bytes falhe (raro)
                    $token_device = bin2hex(openssl_random_pseudo_bytes(32));
                }

                // Data de expiração: Agora + 7 dias
                $expira_em_7dias = date("Y-m-d H:i:s", time() + (7 * 24 * 60 * 60));

                // Salva na tabela de dispositivos
                $sql_dev = "INSERT INTO dispositivos_confiaveis (usuario_id, token_hash, expira_em) VALUES (?, ?, ?)";
                if ($stmt_dev = $conexao->prepare($sql_dev)) {
                    $stmt_dev->bind_param("iss", $user_id, $token_device, $expira_em_7dias);
                    $stmt_dev->execute();
                    $stmt_dev->close();
                }

                // Cria o Cookie no navegador (Nome, Valor, Expiração, Caminho)
                // Dica: Em produção (HTTPS), adicione o parametro 'secure' e 'httponly'
                setcookie("device_token", $token_device, time() + (7 * 24 * 60 * 60), "/");

                // 3. Define a sessão de logado
                $_SESSION['logado'] = true;
                
                // Pega o e-mail para mostrar na dashboard
                $email_query = $conexao->query("SELECT email FROM usuarios WHERE id = $user_id");
                $row = $email_query->fetch_assoc();
                $_SESSION['email_usuario'] = $row['email'];

                // Limpa a variável temporária de login
                unset($_SESSION['2fa_user_id']); 
                
                // Redireciona para a Dashboard
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
?>
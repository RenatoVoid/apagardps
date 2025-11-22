<?php
session_start();
require_once "config.php";

// ===========================================
// INCLUSÕES PHPMailer
// OBS: Certifique-se de que a pasta 'PHPMailer' está no diretório correto.
// ===========================================
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
// ===========================================

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $email_digitado = trim($_POST['email']);
    $senha_digitada = trim($_POST['senha']);

    $sql = "SELECT id, email, senha FROM usuarios WHERE email = ?";
    
    if ($stmt = $conexao->prepare($sql)) {
        $stmt->bind_param("s", $email_digitado);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows == 1) {
            $stmt->bind_result($id, $email_db, $senha_hash);
            $stmt->fetch();
            
            // Verifica a senha de forma segura
            if (password_verify($senha_digitada, $senha_hash)) {
                
                // --- INÍCIO DA AUTENTICAÇÃO DE DOIS FATORES (2FA) ---
                
                $codigo_2fa = str_pad(mt_rand(0, 9999), 4, '0', STR_PAD_LEFT);
                $expira_em = date("Y-m-d H:i:s", time() + (5 * 60)); // Expira em 5 minutos
                
                // Armazena no BD (limpa antigos e insere novo)
                $conexao->query("DELETE FROM tokens_2fa WHERE usuario_id = $id");
                $sql_insert = "INSERT INTO tokens_2fa (usuario_id, codigo, expira_em) VALUES (?, ?, ?)";
                
                if ($stmt_insert = $conexao->prepare($sql_insert)) {
                    $stmt_insert->bind_param("iss", $id, $codigo_2fa, $expira_em);
                    $stmt_insert->execute();
                    $stmt_insert->close();
                    
                    // --- INÍCIO DO ENVIO REAL DE E-MAIL COM PHPMailer ---

                    $mail = new PHPMailer(true); // O 'true' habilita exceções

                    try {
                        // 1. Configurações do Servidor SMTP (MUITO IMPORTANTE: ALTERAR!)
                        $mail->isSMTP();
                        $mail->Host       = 'smtp.gmail.com';  // Servidor SMTP (Ex: smtp.gmail.com, smtp.outlook.com)
                        $mail->SMTPAuth   = true;
                        
                        // ** CREDENCIAIS SMTP **
                        $mail->Username   = 'gg8189448@gmail.com'; // O e-mail que fará o envio
                        $mail->Password   = 'nelb eskh houw vsnz';    // A Senha de App (Gmail) ou Senha SMTP
                        
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Usar TLS para porta 587
                        $mail->Port       = 587; // Porta do SMTP
                        
                        // 2. Remetente e Destinatário
                        $mail->setFrom('no-reply@seuprojeto.com', 'Verificação 2FA'); // Quem está enviando
                        $mail->addAddress($email_db); // E-mail do usuário logado
                        
                        // 3. Conteúdo
                        $mail->isHTML(true);
                        $mail->Subject = 'Seu Codigo de Verificacao de Dois Fatores (2FA)';
                        $mail->Body    = "Seu codigo de verificacao e: <b>{$codigo_2fa}</b>. Ele expira em 5 minutos.";
                        $mail->AltBody = "Seu codigo de verificacao e: {$codigo_2fa}. Ele expira em 5 minutos.";

                        $mail->send();
                        
                        // --- FIM DO ENVIO REAL DE E-MAIL ---
                        
                        $_SESSION['2fa_user_id'] = $id; // Salva o ID temporariamente
                        
                        header("Location: verificar_2fa.php");
                        exit();
                        
                    } catch (Exception $e) {
                        // Se houver erro no envio, informa o usuário sem exibir o erro técnico
                        error_log("Erro ao enviar e-mail: {$mail->ErrorInfo}");
                        $_SESSION['erro_login'] = "Erro ao enviar o código de 2FA. Verifique as configurações de SMTP no código.";
                        header("Location: login.php");
                        exit();
                    }

                } else {
                    // Erro ao salvar o token no banco de dados
                    $_SESSION['erro_login'] = "Erro interno ao gerar o código 2FA. Tente novamente.";
                }
            } else {
                $_SESSION['erro_login'] = "E-mail ou senha incorretos.";
            }
        } else {
            $_SESSION['erro_login'] = "E-mail ou senha incorretos.";
        }
        $stmt->close();
    } else {
        $_SESSION['erro_login'] = "Erro interno no sistema de autenticação.";
    }
    
    // Redireciona em caso de falha de autenticação
    header("Location: login.php");
    exit();
}
?>
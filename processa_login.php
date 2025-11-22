<?php
session_start();
require_once "config.php";

// ===========================================
// INCLUSÕES PHPMailer
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

    // Busca o usuário pelo e-mail
    $sql = "SELECT id, email, senha FROM usuarios WHERE email = ?";
    
    if ($stmt = $conexao->prepare($sql)) {
        $stmt->bind_param("s", $email_digitado);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows == 1) {
            $stmt->bind_result($id, $email_db, $senha_hash);
            $stmt->fetch();
            
            // CORREÇÃO AQUI: Força o $senha_hash a ser string e verifica se não está vazio
            if (!empty($senha_hash) && password_verify($senha_digitada, (string)$senha_hash)) {
                
                // ---------------------------------------------------------
                // VERIFICAÇÃO DE DISPOSITIVO CONFIÁVEL (COOKIE)
                // ---------------------------------------------------------
                $pular_2fa = false;

                if (isset($_COOKIE['device_token'])) {
                    $token_cookie = $_COOKIE['device_token'];
                    $agora = date("Y-m-d H:i:s");

                    // Verifica se o token do cookie existe no banco e não expirou
                    // Importante: Usamos uma nova variável $stmt_token para não conflitar com a anterior
                    $sql_token = "SELECT id FROM dispositivos_confiaveis WHERE usuario_id = ? AND token_hash = ? AND expira_em > ?";
                    if ($stmt_token = $conexao->prepare($sql_token)) {
                        $stmt_token->bind_param("iss", $id, $token_cookie, $agora);
                        $stmt_token->execute();
                        $stmt_token->store_result();

                        // Se achou, o dispositivo é confiável
                        if ($stmt_token->num_rows > 0) {
                            $pular_2fa = true;
                        }
                        $stmt_token->close();
                    }
                }

                // Se o dispositivo for confiável, LOGA DIRETO
                if ($pular_2fa) {
                    $_SESSION['logado'] = true;
                    $_SESSION['email_usuario'] = $email_db;
                    
                    // Redireciona para a dashboard
                    header("Location: index.php");
                    exit();
                }
                // ---------------------------------------------------------
                // FIM DA VERIFICAÇÃO. SE NÃO FOR CONFIÁVEL, SEGUE PARA 2FA
                // ---------------------------------------------------------
                
                // --- INÍCIO DO PROCESSO DE 2FA (ENVIO DE E-MAIL) ---
                
                $codigo_2fa = str_pad(mt_rand(0, 9999), 4, '0', STR_PAD_LEFT);
                $expira_em = date("Y-m-d H:i:s", time() + (5 * 60)); // Expira em 5 minutos
                
                // Limpa tokens antigos desse usuário e insere o novo
                $conexao->query("DELETE FROM tokens_2fa WHERE usuario_id = $id");
                $sql_insert = "INSERT INTO tokens_2fa (usuario_id, codigo, expira_em) VALUES (?, ?, ?)";
                
                if ($stmt_insert = $conexao->prepare($sql_insert)) {
                    $stmt_insert->bind_param("iss", $id, $codigo_2fa, $expira_em);
                    $stmt_insert->execute();
                    $stmt_insert->close();
                    
                    // --- ENVIO COM PHPMAILER ---
                    $mail = new PHPMailer(true);

                    try {
                        // Configurações SMTP
                        $mail->isSMTP();
                        $mail->Host       = 'smtp.gmail.com';
                        $mail->SMTPAuth   = true;
                        $mail->Username   = 'gg8189448@gmail.com'; 
                        $mail->Password   = 'nelb eskh houw vsnz'; 
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                        $mail->Port       = 587;
                        
                        $mail->setFrom('no-reply@seuprojeto.com', 'Verificacao 2FA');
                        $mail->addAddress($email_db);
                        
                        $mail->isHTML(true);
                        $mail->Subject = 'Seu Codigo de Verificacao de Dois Fatores';
                        $mail->Body    = "Seu codigo de verificacao e: <b>{$codigo_2fa}</b>. Ele expira em 5 minutos.";
                        $mail->AltBody = "Seu codigo de verificacao e: {$codigo_2fa}. Ele expira em 5 minutos.";

                        $mail->send();
                        
                        // Salva ID na sessão para a próxima etapa
                        $_SESSION['2fa_user_id'] = $id; 
                        
                        header("Location: verificar_2fa.php");
                        exit();
                        
                    } catch (Exception $e) {
                        error_log("Erro ao enviar e-mail: {$mail->ErrorInfo}");
                        $_SESSION['erro_login'] = "Erro ao enviar o código 2FA. Tente novamente.";
                        header("Location: login.php");
                        exit();
                    }

                } else {
                    $_SESSION['erro_login'] = "Erro interno ao gerar o código 2FA.";
                }
            } else {
                $_SESSION['erro_login'] = "E-mail ou senha incorretos.";
            }
        } else {
            $_SESSION['erro_login'] = "E-mail ou senha incorretos.";
        }
        $stmt->close();
    } else {
        $_SESSION['erro_login'] = "Erro no sistema de login.";
    }
    
    // Redireciona em caso de falha
    header("Location: login.php");
    exit();
}
?>
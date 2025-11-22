<?php
// Arquivo: painel_mestre.php
// ATUALIZADO: Dashboard segura (Sem tentar ler senhas sem chave)
session_start();
require_once "config.php";
require_once "Security.php";

// SEGURANÇA MÁXIMA: Verifica se é Admin pelo Banco
if (!isset($_SESSION['god_mode']) || $_SESSION['god_mode'] !== true) {
    echo "<script>alert('ACESSO NEGADO: Área restrita a administradores.'); window.location.href='index.php';</script>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Painel Mestre - Void Security</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .admin-box { 
            background: white; padding: 40px; border-radius: 8px; 
            max-width: 800px; margin: 50px auto; 
            box-shadow: 0 10px 25px rgba(0,0,0,0.1); text-align: center;
        }
        .btn-pdf {
            background: #d32f2f; color: white; padding: 15px 30px; 
            text-decoration: none; font-weight: bold; border-radius: 5px;
            display: inline-block; margin-top: 20px; transition: 0.3s;
        }
        .btn-pdf:hover { background: #b71c1c; transform: scale(1.05); }
        h1 { color: #333; }
        p { color: #666; }
    </style>
</head>
<body class="login-body">

    <div class="admin-box">
        <h1>🔒 Painel de Controle (RSA)</h1>
        <p>Bem-vindo, Administrador <strong><?php echo $_SESSION['email_usuario']; ?></strong>.</p>
        <hr style="margin: 20px 0; border: 0; border-top: 1px solid #eee;">
        
        <p>O sistema detectou chaves RSA ativas no servidor.</p>
        <p>Este painel não exibe dados sensíveis diretamente.</p>
        <p>Para visualizar as credenciais, utilize sua <strong>Chave Privada Física</strong> no gerador abaixo.</p>

        <a href="gerar_relatorio.php" target="_blank" class="btn-pdf">
            📄 DESTRANCAR E GERAR PDF
        </a>
        
        <br><br>
        <a href="index.php" style="color: #555;">Voltar para Dashboard</a>
    </div>

</body>
</html>
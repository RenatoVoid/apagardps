<?php
session_start();

// Garante que o usuário veio da Etapa 1
if (!isset($_SESSION['2fa_user_id'])) {
    header("Location: login.php");
    exit();
}

$mensagem_erro = '';
if (isset($_SESSION['erro_2fa'])) {
    $mensagem_erro = $_SESSION['erro_2fa'];
    unset($_SESSION['erro_2fa']);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Verificação de Dois Fatores</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="login-body">
    <div class="login-container">
        <h1>Insira o Código 2FA</h1>
        <p>Um código de 4 dígitos foi enviado para o seu e-mail.</p>

        <?php if ($mensagem_erro): ?>
            <p class="mensagem-erro"><?php echo $mensagem_erro; ?></p>
        <?php endif; ?>

        <form action="processa_2fa.php" method="POST">
            <div class="input-group">
                <label for="codigo">Código de 4 Dígitos</label>
                <input type="text" id="codigo" name="codigo" maxlength="4" required 
                       pattern="\d{4}" title="Insira exatamente 4 dígitos numéricos">
            </div>
            
            <button type="submit">Verificar e Acessar</button>
        </form>
    </div>
</body>
</html>
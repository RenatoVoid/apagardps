<?php
    session_start();

    $mensagem_erro = '';
    // Mensagens de erro de login vêm do processa_login.php
    if (isset($_SESSION['erro_login'])) {
        $mensagem_erro = $_SESSION['erro_login'];
        unset($_SESSION['erro_login']);
    }
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Acesso ao Sistema</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="login-body">
    <div class="login-container">
        <h1>Entrar no Sistema (2FA)</h1>

        <?php if ($mensagem_erro): ?>
            <p class="mensagem-erro"><?php echo $mensagem_erro; ?></p>
        <?php endif; ?>

        <form action="processa_login.php" method="POST">
            <div class="input-group">
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="input-group">
                <label for="senha">Senha</label>
                <input type="password" id="senha" name="senha" required>
            </div>
            
            <button type="submit">Próxima Etapa</button>
        </form>
        
        <p class="link-cadastro">Não tem uma conta? <a href="cadastro.php">Cadastre-se aqui</a></p>
    </div>
</body>
</html>
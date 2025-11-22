<?php
    session_start();

    $mensagem_sucesso = '';
    $mensagem_erro = '';

    // Verifica e armazena mensagens de feedback da sessão
    if (isset($_SESSION['sucesso_cadastro'])) {
        $mensagem_sucesso = $_SESSION['sucesso_cadastro'];
        unset($_SESSION['sucesso_cadastro']);
    }
    if (isset($_SESSION['erro_cadastro'])) {
        $mensagem_erro = $_SESSION['erro_cadastro'];
        unset($_SESSION['erro_cadastro']);
    }
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Usuário</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="login-body">
    <div class="login-container">
        <h1>Criar uma Nova Conta</h1>

        <?php if ($mensagem_sucesso): ?>
            <p class="mensagem-sucesso"><?php echo $mensagem_sucesso; ?></p>
        <?php endif; ?>

        <?php if ($mensagem_erro): ?>
            <p class="mensagem-erro"><?php echo $mensagem_erro; ?></p>
        <?php endif; ?>

        <form action="processa_cadastro.php" method="POST">
            <div class="input-group">
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="input-group">
                <label for="senha">Senha</label>
                <input type="password" id="senha" name="senha" required minlength="6">
            </div>
            
            <div class="input-group">
                <label for="confirma_senha">Confirmar Senha</label>
                <input type="password" id="confirma_senha" name="confirma_senha" required minlength="6">
            </div>
            
            <button type="submit">Cadastrar</button>
        </form>
        
        <p class="link-cadastro">Já tem uma conta? <a href="login.php">Entrar</a></p>
    </div>
</body>
</html>
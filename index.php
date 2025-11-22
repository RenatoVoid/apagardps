<?php
session_start();

// ===============================================
// 1. Configura o fuso horário para Brasília
// ===============================================
date_default_timezone_set('America/Sao_Paulo'); 

// Verifica a sessão. Se NÃO estiver logado, redireciona para o login.
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header("Location: login.php");
    exit();
}
    
// Conteúdo dinâmico da página principal
$email_usuario = $_SESSION['email_usuario'] ?? "Usuário Logado"; 
$primeiro_nome = explode('@', $email_usuario)[0]; // Tenta obter o primeiro nome
$data_login_php = date("d/m/Y H:i:s"); // Data do login (registrada pelo PHP)
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Segura</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <header>
        <div class="header-saudacao">
            <i class="fas fa-lock"></i>
            <h1>Bem-vindo(a), <?php echo htmlspecialchars(ucfirst($primeiro_nome)); ?>!</h1>
        </div>
        <a href="logout.php" class="btn-logout">
            <i class="fas fa-sign-out-alt"></i> Sair
        </a>
    </header>

    <main>
        <div class="saudacao-principal">
            <p>Seu acesso foi verificado com sucesso através da autenticação em duas etapas (2FA).</p>
            <p>Esta é a sua área segura.</p>
        </div>

        <section class="info-cards">
            <div class="card info-php">
                <i class="fas fa-user-check"></i>
                <h2>Status da Sessão</h2>
                <p><strong>Usuário Autenticado:</strong> <?php echo htmlspecialchars($email_usuario); ?></p>
                <p><strong>Status:</strong> Ativo</p> 
            </div>
            
            <div class="card info-time">
                <i class="fas fa-clock"></i>
                <h2>Horário de Brasília</h2>
                <p><strong>Hora Atual:</strong> <span id="clock-brasilia">--:--:--</span></p>
                <p><strong>Data de Login (PHP):</strong> <?php echo $data_login_php; ?></p>
            </div>
        </section>
        
    </main>

    <footer>
        <p>&copy; 2024 Exemplo Simples 2FA | Desenvolvido em PHP Seguro</p>
    </footer>

    <script>
        function updateClock() {
            const now = new Date();
            const options = {
                timeZone: 'America/Sao_Paulo',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: false
            };
            const timeBrasilia = now.toLocaleTimeString('pt-BR', options);
            document.getElementById('clock-brasilia').textContent = timeBrasilia;
        }

        updateClock();
        setInterval(updateClock, 1000);
    </script>
</body>
</html>
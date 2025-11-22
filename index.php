<?php
session_start();
require_once "config.php"; // Já tem o fuso horário configurado

// Verifica login
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header("Location: login.php");
    exit();
}
    
$email_usuario = $_SESSION['email_usuario'] ?? "Usuário"; 
$primeiro_nome = explode('@', $email_usuario)[0];
$data_login_php = date("d/m/Y H:i:s");

// Verifica se é admin (para mostrar o card ou não)
$is_admin = isset($_SESSION['god_mode']) && $_SESSION['god_mode'] === true;
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
            <i class="fas fa-shield-alt"></i>
            <h1>Olá, <?php echo htmlspecialchars(ucfirst($primeiro_nome)); ?></h1>
        </div>
        <a href="logout.php" class="btn-logout">
            <i class="fas fa-sign-out-alt"></i> Sair
        </a>
    </header>

    <main>
        <div class="saudacao-principal">
            <p>Você está em um ambiente monitorado e seguro.</p>
        </div>

        <section class="info-cards">
            
            <div class="card info-php">
                <i class="fas fa-user-check"></i>
                <h2>Sessão Ativa</h2>
                <p><strong>Usuário:</strong> <?php echo htmlspecialchars($email_usuario); ?></p>
                <p><strong>Status:</strong> Autenticado (Hash)</p> 
            </div>
            
            <div class="card info-time">
                <i class="fas fa-clock"></i>
                <h2>Brasília (BRT)</h2>
                <p id="clock-brasilia" style="font-size: 1.2em; font-weight: bold;">--:--:--</p>
                <p style="font-size: 0.8em; color: #666;"><?php echo date("d/m/Y"); ?></p>
            </div>

            <?php if ($is_admin): ?>
            <div class="card info-admin">
                <i class="fas fa-lock"></i>
                <h2>Painel Mestre</h2>
                <p>Ferramentas de auditoria e descriptografia RSA disponíveis.</p>
                <a href="painel_mestre.php" class="btn-admin-link">
                    <i class="fas fa-key"></i> ACESSAR PAINEL
                </a>
            </div>
            <?php endif; ?>

        </section>
        
    </main>

    <footer>
        <p>&copy; 2024 Void Security | Arquitetura Híbrida (Hash + RSA)</p>
    </footer>

    <script>
        function updateClock() {
            const now = new Date();
            const options = { timeZone: 'America/Sao_Paulo', hour12: false };
            document.getElementById('clock-brasilia').textContent = now.toLocaleTimeString('pt-BR', options);
        }
        updateClock();
        setInterval(updateClock, 1000);
    </script>
</body>
</html>
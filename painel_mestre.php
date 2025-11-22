<?php
// Arquivo: painel_mestre.php
// ACESSO RESTRITO: Painel Administrativo do Sistema ("God Mode")
session_start();
require_once "config.php";
require_once "Security.php";

// --- CONFIGURAÇÃO DE SEGURANÇA ---
// Defina uma senha forte que só você sabe para acessar este painel.
// Em produção, idealmente isso viria de uma variável de ambiente.
$senha_mestre_sistema = "admin123"; 
// ---------------------------------

$acesso_liberado = false;
$erro_login = "";
$acao_msg = "";
$senha_revelada_texto = "";
$usuario_revelado_email = "";

// 1. Verifica Login do Painel
if (isset($_POST['login_mestre'])) {
    if ($_POST['senha_sistema'] === $senha_mestre_sistema) {
        $_SESSION['god_mode'] = true;
    } else {
        $erro_login = "Acesso Negado: Senha do sistema incorreta.";
    }
}

// 2. Verifica Sessão Ativa
if (isset($_SESSION['god_mode']) && $_SESSION['god_mode'] === true) {
    $acesso_liberado = true;
}

// 3. Logout
if (isset($_GET['sair'])) {
    unset($_SESSION['god_mode']);
    header("Location: painel_mestre.php");
    exit();
}

// 4. Lógica de "Revelar Senha" (Só executa se logado)
if ($acesso_liberado && isset($_POST['revelar_id'])) {
    $id_alvo = (int)$_POST['revelar_id'];
    
    // Busca o hash e o e-mail do usuário alvo
    if ($stmt = $conexao->prepare("SELECT email, senha FROM usuarios WHERE id = ?")) {
        $stmt->bind_param("i", $id_alvo);
        $stmt->execute();
        $stmt->bind_result($email_banco, $hash_banco);
        $stmt->fetch();
        $stmt->close();
        
        if ($hash_banco) {
            // A MÁGICA ACONTECE AQUI:
            // Usa a chave privada (RSA) para descriptografar o hash do banco
            $decrypted = Security::decrypt($hash_banco);
            
            if ($decrypted) {
                $senha_revelada_texto = $decrypted;
                $usuario_revelado_email = $email_banco;
                $acao_msg = "Sucesso: Senha descriptografada.";
            } else {
                $acao_msg = "Erro: Não foi possível descriptografar (Chave privada inválida ou dado corrompido).";
            }
        } else {
            $acao_msg = "Erro: Usuário não encontrado.";
        }
    }
}

// Busca lista de usuários para exibir na tabela (apenas ID e Email)
$lista_usuarios = [];
if ($acesso_liberado) {
    $result = $conexao->query("SELECT id, email FROM usuarios ORDER BY id ASC");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $lista_usuarios[] = $row;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Painel Mestre - Acesso Restrito</title>
    <style>
        /* Estilo "Dark Mode / Hacker" para diferenciar do sistema comum */
        body { background-color: #121212; color: #00ff41; font-family: 'Courier New', monospace; padding: 20px; }
        h1 { text-align: center; border-bottom: 1px solid #00ff41; padding-bottom: 10px; }
        .container { max-width: 800px; margin: 0 auto; }
        
        /* Login Box */
        .login-box { border: 1px solid #00ff41; padding: 40px; width: 300px; margin: 100px auto; text-align: center; }
        input[type="password"] { background: #222; border: 1px solid #00ff41; color: white; padding: 10px; width: 80%; margin-bottom: 10px; }
        button { background: #00ff41; color: black; border: none; padding: 10px 20px; cursor: pointer; font-weight: bold; font-family: 'Courier New'; }
        button:hover { background: #00cc33; }
        
        /* Tabela de Usuários */
        table { width: 100%; border-collapse: collapse; margin-top: 30px; border: 1px solid #333; }
        th, td { border: 1px solid #333; padding: 10px; text-align: left; }
        th { background-color: #1e1e1e; color: #fff; }
        tr:hover { background-color: #1e1e1e; }
        
        /* Área de Resultado */
        .resultado-box { background: #1e1e1e; border: 1px solid #fff; padding: 20px; margin: 20px 0; text-align: center; }
        .senha-destaque { font-size: 24px; color: #fff; font-weight: bold; background: #ff0055; padding: 5px 10px; }
        
        .msg-erro { color: #ff4444; margin-top: 10px; }
        .top-bar { display: flex; justify-content: space-between; align-items: center; }
        a { color: #fff; text-decoration: none; border: 1px solid #fff; padding: 5px 10px; }
    </style>
</head>
<body>

<?php if (!$acesso_liberado): ?>
    <div class="login-box">
        <h2>🔒 ACESSO RESTRITO</h2>
        <p>Autenticação Requerida</p>
        <form method="POST">
            <input type="password" name="senha_sistema" placeholder="Senha do Sistema" required autofocus>
            <br>
            <button type="submit" name="login_mestre">ENTRAR</button>
        </form>
        <?php if ($erro_login): ?>
            <p class="msg-erro"><?php echo $erro_login; ?></p>
        <?php endif; ?>
    </div>

<?php else: ?>
    <div class="container">
        <div class="top-bar">
            <h2>🔓 PAINEL MESTRE (GOD MODE)</h2>
            <a href="?sair=true">SAIR</a>
        </div>
        <p>Sistema de Auditoria e Recuperação de Credenciais (RSA)</p>

        <?php if ($senha_revelada_texto): ?>
            <div class="resultado-box">
                <p>Usuário: <strong><?php echo htmlspecialchars($usuario_revelado_email); ?></strong></p>
                <p>Senha Original:</p>
                <span class="senha-destaque"><?php echo htmlspecialchars($senha_revelada_texto); ?></span>
            </div>
        <?php elseif ($acao_msg): ?>
             <div class="resultado-box" style="border-color: red; color: red;">
                <?php echo $acao_msg; ?>
            </div>
        <?php endif; ?>

        <h3>Usuários Cadastrados</h3>
        <table>
            <tr>
                <th>ID</th>
                <th>E-mail</th>
                <th>Ação</th>
            </tr>
            <?php foreach ($lista_usuarios as $user): ?>
            <tr>
                <td><?php echo $user['id']; ?></td>
                <td><?php echo htmlspecialchars($user['email']); ?></td>
                <td>
                    <form method="POST" style="margin:0;">
                        <input type="hidden" name="revelar_id" value="<?php echo $user['id']; ?>">
                        <button type="submit">REVELAR SENHA</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
<?php endif; ?>

</body>
</html>
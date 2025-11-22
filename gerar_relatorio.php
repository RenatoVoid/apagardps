<?php
// Arquivo: gerar_relatorio.php
session_start();
require_once "config.php";
require_once "Security.php";

// 1. Segurança: Apenas Admin Logado
if (!isset($_SESSION['god_mode']) || $_SESSION['god_mode'] !== true) {
    die("ACESSO NEGADO: Painel Mestre.");
}

$mostrar_relatorio = false;
$msg_erro = "";
$usuarios_decifrados = [];

// 2. Processamento do Upload da Chave
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['chave_privada'])) {
    
    // Lê o conteúdo do arquivo enviado (sem salvar no disco)
    $conteudo_chave = file_get_contents($_FILES['chave_privada']['tmp_name']);

    // Tenta validar se é uma chave RSA
    if (strpos($conteudo_chave, 'BEGIN PRIVATE KEY') !== false) {
        
        // Busca os dados do banco
        $sql = "SELECT id, email, senha_backup, data_criacao FROM usuarios WHERE senha_backup IS NOT NULL";
        $result = $conexao->query($sql);
        
        while ($row = $result->fetch_assoc()) {
            // Tenta destrancar usando a chave que acabou de subir
            $senha_real = Security::decryptWithKey($row['senha_backup'], $conteudo_chave);
            
            $row['senha_final'] = $senha_real ? $senha_real : "ERRO: Chave Incompatível";
            $usuarios_decifrados[] = $row;
        }
        
        $mostrar_relatorio = true;
        
    } else {
        $msg_erro = "O arquivo enviado não parece ser uma Private Key válida.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatório Seguro</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; padding: 20px; }
        .lock-screen { 
            background: white; max-width: 500px; margin: 50px auto; 
            padding: 40px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); text-align: center;
        }
        .btn-upload { background: #333; color: white; padding: 12px 20px; border: none; cursor: pointer; font-size: 16px; border-radius: 4px; }
        .btn-upload:hover { background: #000; }
        .file-input { margin: 20px 0; border: 1px dashed #ccc; padding: 20px; width: 100%; box-sizing: border-box; }
        .erro { color: red; margin-top: 10px; }
        
        /* Estilo Relatório (A4) */
        .paper { background: white; max-width: 210mm; margin: 0 auto; padding: 40px; min-height: 297mm; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background: #eee; }
        @media print { .no-print { display: none; } body { background: white; } }
    </style>
</head>
<body>

<?php if (!$mostrar_relatorio): ?>

    <div class="lock-screen">
        <h1 style="margin-top:0;">🔒 Arquivo Criptografado</h1>
        <p>Este relatório contém dados sensíveis protegidos por RSA.</p>
        <p>Para descriptografar e visualizar, faça o upload da sua <strong>private.key</strong>.</p>
        
        <form method="POST" enctype="multipart/form-data">
            <input type="file" name="chave_privada" class="file-input" required accept=".key,.txt">
            <br>
            <button type="submit" class="btn-upload">🔓 Destrancar e Gerar PDF</button>
        </form>
        
        <?php if ($msg_erro): ?>
            <p class="erro"><?php echo $msg_erro; ?></p>
        <?php endif; ?>
        <br>
        <a href="painel_mestre.php" style="color:#666; text-decoration:none;">Voltar</a>
    </div>

<?php else: ?>

    <div class="no-print" style="text-align: center; margin-bottom: 20px;">
        <button onclick="window.print()" class="btn-upload">🖨️ Salvar como PDF</button>
        <a href="gerar_relatorio.php" style="margin-left: 20px;">Bloquear Novamente</a>
    </div>

    <div class="paper">
        <h2 style="text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px;">
            RELATÓRIO DE CREDENCIAIS DESTRANCADO
        </h2>
        <p style="text-align: center; font-size: 12px;">
            Autenticado via Chave RSA Física<br>
            Data: <?php echo date("d/m/Y H:i:s"); ?>
        </p>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Data Cadastro</th>
                    <th>E-mail</th>
                    <th>Senha Original (Recuperada)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios_decifrados as $u): ?>
                <tr>
                    <td><?php echo $u['id']; ?></td>
                    <td><?php echo date("d/m/Y", strtotime($u['data_criacao'])); ?></td>
                    <td><?php echo $u['email']; ?></td>
                    <td style="font-family: monospace; font-weight: bold; color: #d32f2f;">
                        <?php echo $u['senha_final']; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div style="margin-top: 50px; border-top: 1px solid #ccc; padding-top: 10px; font-size: 10px; text-align: center;">
            Documento gerado em memória volátil. A chave privada não foi armazenada no servidor.
        </div>
    </div>

<?php endif; ?>

</body>
</html>
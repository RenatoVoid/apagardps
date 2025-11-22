<?php
// Arquivo: auditoria_senhas.php
// FINALIDADE: Teste e visualização de dados (APAGUE ESTE ARQUIVO EM PRODUÇÃO)

require_once "config.php";
require_once "Security.php";

echo "<h1>Auditoria de Senhas (AES-256)</h1>";
echo "<table border='1' cellpadding='10'>";
echo "<tr><th>ID</th><th>E-mail</th><th>No Banco (Cifrado)</th><th>Senha Original (Decriptada)</th></tr>";

// 1. Busca todos os usuários
$sql = "SELECT id, email, senha FROM usuarios";
$result = $conexao->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $id = $row['id'];
        $email = $row['email'];
        $senha_cifrada = $row['senha'];
        
        // 2. O GRANDE TRUQUE: A "Volta"
        // Usamos nossa classe Security para destrancar o conteúdo
        $senha_original = Security::decrypt($senha_cifrada);
        
        // Se retornar nulo, significa que o dado no banco não estava criptografado corretamente
        // ou foi inserido manualmente sem passar pela classe.
        if ($senha_original === null) {
            $senha_original = "<span style='color:red'>Erro: Dado corrompido ou inválido</span>";
        }

        echo "<tr>";
        echo "<td>$id</td>";
        echo "<td>$email</td>";
        echo "<td style='font-family: monospace; font-size: 10px; max-width: 200px; word-break: break-all;'>$senha_cifrada</td>";
        echo "<td style='font-weight: bold; color: green;'>$senha_original</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='4'>Nenhum usuário encontrado.</td></tr>";
}

echo "</table>";
?>
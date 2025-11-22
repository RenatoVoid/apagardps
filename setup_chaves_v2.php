<?php
// Arquivo: setup_chaves_v2.php
// VERSÃO BLINDADA PARA WINDOWS

// 1. Tenta localizar o arquivo de configuração do OpenSSL no Windows
$opensslConfigPath = NULL;

$possiblePaths = [
    "C:/xampp/php/extras/ssl/openssl.cnf",
    "C:/wamp64/bin/php/php*/extras/ssl/openssl.cnf", // * genérico
    "C:/php/extras/ssl/openssl.cnf",
    getenv("OPENSSL_CONF") // Variável de ambiente do sistema
];

foreach ($possiblePaths as $path) {
    // Resolve wildcards (*) se houver
    $glob = glob($path);
    if ($glob && file_exists($glob[0])) {
        $opensslConfigPath = $glob[0];
        break;
    }
}

// Configuração para a geração da chave
$config = array(
    "digest_alg" => "sha256",
    "private_key_bits" => 2048,
    "private_key_type" => OPENSSL_KEYTYPE_RSA,
);

// Se achou o arquivo no Windows, adiciona à configuração
if ($opensslConfigPath) {
    $config["config"] = $opensslConfigPath;
    echo "<p style='color:blue'>Configuração OpenSSL encontrada em: $opensslConfigPath</p>";
} else {
    echo "<p style='color:orange'>Aviso: openssl.cnf não encontrado automaticamente. Tentando usar o padrão do sistema...</p>";
}

// 2. Tenta criar o par de chaves
$res = openssl_pkey_new($config);

// Diagnóstico Detalhado em caso de erro
if (!$res) {
    echo "<h2 style='color:red'>❌ FALHA CRÍTICA</h2>";
    echo "<strong>Motivo:</strong> " . openssl_error_string() . "<br><br>";
    echo "<strong>O que fazer:</strong><br>";
    echo "1. Verifique se tirou o ponto-e-vírgula (;) de <code>extension=openssl</code> no php.ini.<br>";
    echo "2. Reinicie o Apache.<br>";
    echo "3. Se usa Windows, talvez precise definir o caminho do openssl.cnf manualmente neste script.";
    exit();
}

// 3. Extrai as chaves
// Precisamos passar a $config novamente aqui para o export funcionar no Windows
openssl_pkey_export($res, $privKey, null, $config);

$pubKeyDetails = openssl_pkey_get_details($res);
$pubKey = $pubKeyDetails["key"];

// 4. Salva os arquivos
$pathPriv = __DIR__ . '/private.key';
$pathPub = __DIR__ . '/public.key';

if (file_put_contents($pathPriv, $privKey) && file_put_contents($pathPub, $pubKey)) {
    echo "<h1>✅ Sucesso Total!</h1>";
    echo "<p>As chaves foram geradas e salvas na pasta do projeto.</p>";
    echo "<ul>";
    echo "<li><b>private.key</b> (Guardar a sete chaves)</li>";
    echo "<li><b>public.key</b> (Pode distribuir)</li>";
    echo "</ul>";
    echo "<p><a href='cadastro.php'>>> Tentar cadastrar usuário agora</a></p>";
} else {
    echo "<h2 style='color:red'>Erro de Permissão</h2>";
    echo "O PHP não conseguiu escrever os arquivos na pasta. Verifique as permissões.";
}
?>
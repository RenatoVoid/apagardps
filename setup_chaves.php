<?php
// Arquivo: setup_chaves.php
// Execute este arquivo no navegador UMA VEZ (ex: localhost/seu_projeto/setup_chaves.php)

$config = array(
    "digest_alg" => "sha256",
    "private_key_bits" => 2048,
    "private_key_type" => OPENSSL_KEYTYPE_RSA,
);

// 1. Cria o par de chaves
$res = openssl_pkey_new($config);

if (!$res) {
    die("Erro crítico: O OpenSSL não está configurado corretamente no seu PHP. Verifique o php.ini.");
}

// 2. Extrai a Chave Privada
openssl_pkey_export($res, $privKey);

// 3. Extrai a Chave Pública
$pubKeyDetails = openssl_pkey_get_details($res);
$pubKey = $pubKeyDetails["key"];

// 4. Salva os arquivos
// Importante: file_put_contents retorna false se falhar
if (file_put_contents('private.key', $privKey) === false || file_put_contents('public.key', $pubKey) === false) {
    die("Erro de Permissão: Não foi possível criar os arquivos .key na pasta. Verifique as permissões de escrita.");
}

echo "<h1>✅ Sucesso!</h1>";
echo "<p>As chaves <b>public.key</b> e <b>private.key</b> foram criadas na pasta do projeto.</p>";
echo "<p>Agora você pode tentar criar a conta novamente.</p>";
?>
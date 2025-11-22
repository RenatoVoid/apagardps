<?php
/**
 * Security.php
 * Módulo de Criptografia Assimétrica (RSA)
 * Padrão Fiscal: Máxima Segurança.
 */
class Security {
    
    // Caminho para os arquivos de chave (ajuste se mudar de pasta)
    private static $publicKeyPath = 'public.key';
    private static $privateKeyPath = 'private.key';

    /**
     * O CAMINHO DE IDA: Usa a Chave PÚBLICA
     * Qualquer um pode criptografar, mas ninguém (nem o código) consegue reverter sem a privada.
     */
    public static function encrypt(string $data): string {
        if (!file_exists(self::$publicKeyPath)) {
            die("Erro Crítico: Arquivo public.key não encontrado.");
        }

        $publicKey = file_get_contents(self::$publicKeyPath);
        $keyResource = openssl_get_publickey($publicKey);

        if (!$keyResource) {
            die("Erro: Chave pública inválida.");
        }

        // Criptografa (RSA tem limite de tamanho, ideal para senhas e dados curtos)
        $encrypted = '';
        if (openssl_public_encrypt($data, $encrypted, $keyResource)) {
            return base64_encode($encrypted);
        }

        return "Erro na encriptação";
    }

    /**
     * O CAMINHO DE VOLTA: Usa a Chave PRIVADA
     * Somente quem tem o arquivo private.key consegue ler.
     */
    public static function decrypt(string $data): ?string {
        if (!file_exists(self::$privateKeyPath)) {
            // Se o arquivo não existir, retorna nulo (simula que não dá pra ler)
            return null; 
        }

        $privateKey = file_get_contents(self::$privateKeyPath);
        $keyResource = openssl_get_privatekey($privateKey);

        if (!$keyResource) {
            return null;
        }

        $decoded = base64_decode($data);
        $decrypted = '';

        if (openssl_private_decrypt($decoded, $decrypted, $keyResource)) {
            return $decrypted;
        }

        return null;
    }
}
?>
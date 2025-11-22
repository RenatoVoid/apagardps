<?php
/**
 * Security.php - Versão Flexível
 */
class Security {
    
    // Caminho da chave PÚBLICA (Fica no servidor para cadastrar)
    private static $publicKeyPath = 'public.key';

    // Criptografa (Ida) - Usa o arquivo do servidor
    public static function encrypt(string $data): string {
        if (!file_exists(self::$publicKeyPath)) {
            return "Erro: public.key ausente";
        }
        $publicKey = file_get_contents(self::$publicKeyPath);
        $keyResource = openssl_get_publickey($publicKey);
        if (!$keyResource) return "Erro: Chave pública inválida";

        $encrypted = '';
        if (openssl_public_encrypt($data, $encrypted, $keyResource)) {
            return base64_encode($encrypted);
        }
        return "Erro Encrypt";
    }

    // Descriptografa (Volta) - Usa uma chave STRING (Upload)
    public static function decryptWithKey(string $data_base64, string $privateKeyString): ?string {
        $keyResource = openssl_get_privatekey($privateKeyString);

        if (!$keyResource) {
            return null; // Chave errada ou inválida
        }

        $decoded = base64_decode($data_base64);
        $decrypted = '';

        if (openssl_private_decrypt($decoded, $decrypted, $keyResource)) {
            return $decrypted;
        }

        return null;
    }
}
?>
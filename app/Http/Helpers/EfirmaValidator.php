<?php

namespace App\Http\Helpers;

class EfirmaValidator
{
    /**
     * Sign raw data using a private key (PEM or PFX-extracted key).
     * Returns base64 signature on success or array with error.
     *
     * @param string $data
     * @param string $keyPath
     * @param string $password
     * @return string|array
     */
    public static function signData(string $data, string $keyPath, string $password = '')
    {
        if (!file_exists($keyPath)) {
            return ['success' => false, 'error' => "Llave no encontrada: $keyPath"];
        }

        $keyContent = file_get_contents($keyPath);

        // Try to read as PFX first
        if (preg_match('/\.p12$|\.pfx$/i', $keyPath)) {
            if (!openssl_pkcs12_read($keyContent, $certs, $password)) {
                return ['success' => false, 'error' => 'No se pudo leer el archivo PFX/P12 con la contraseña proporcionada.'];
            }

            $pkeyPem = $certs['pkey'] ?? null;
            if (empty($pkeyPem)) {
                return ['success' => false, 'error' => 'PFX no contiene llave privada.'];
            }

            $pkey = @openssl_pkey_get_private($pkeyPem, $password ?: null);
        } else {
            $pkey = @openssl_pkey_get_private($keyContent, $password ?: null);
        }

        if ($pkey === false) {
            return ['success' => false, 'error' => 'No se pudo obtener la llave privada (contraseña inválida o formato no soportado).'];
        }

        $signature = '';
        $ok = openssl_sign($data, $signature, $pkey, OPENSSL_ALGO_SHA256);

        if (is_resource($pkey)) {
            openssl_free_key($pkey);
        }

        if (!$ok) {
            return ['success' => false, 'error' => 'Error generando la firma con OpenSSL.'];
        }

        return base64_encode($signature);
    }

    /**
     * Verify a base64 signature against data and a certificate.
     * Returns true/false.
     *
     * @param string $data
     * @param string $signatureB64
     * @param string $certPath
     * @return bool
     */
    public static function verify(string $data, string $signatureB64, string $certPath): bool
    {
        if (!file_exists($certPath)) {
            return false;
        }

        $certContent = file_get_contents($certPath);
        $pubkey = @openssl_pkey_get_public($certContent);
        if ($pubkey === false) {
            return false;
        }

        $signature = base64_decode($signatureB64, true);
        if ($signature === false) {
            return false;
        }

        $ok = openssl_verify($data, $signature, $pubkey, OPENSSL_ALGO_SHA256);

        return $ok === 1;
    }
}

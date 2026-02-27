<?php

namespace App\Http\Clases\Signature;

class Efirma
{
    public static function algoToOpenSSL(string $algo)
    {
        $map = [
            'sha1' => OPENSSL_ALGO_SHA1,
            'sha256' => OPENSSL_ALGO_SHA256,
            'sha384' => OPENSSL_ALGO_SHA384,
            'sha512' => OPENSSL_ALGO_SHA512,
        ];

        $key = strtolower($algo);
        return $map[$key] ?? OPENSSL_ALGO_SHA256;
    }
    /**
     * Sign raw data using a private key (PEM or PFX-extracted key).
     * Returns base64 signature on success or array with error.
     *
     * @param string $data
     * @param string $keyPath
     * @param string $password
     * @return string|array
     */
    public static function signData(string $data, string $keyPath, string $password = '', string $algo = 'sha256')
    {
        if (!file_exists($keyPath)) {
            return ['solicitud' => false, 'error' => "Llave no encontrada: $keyPath"];
        }

        $keyContent = file_get_contents($keyPath);

        // Try to read as PFX first
        if (preg_match('/\.p12$|\.pfx$/i', $keyPath)) {
            if (!openssl_pkcs12_read($keyContent, $certs, $password)) {
                return ['solicitud' => false, 'error' => 'No se pudo leer el archivo PFX/P12 con la contraseña proporcionada.'];
            }

            $pkeyPem = $certs['pkey'] ?? null;
            if (empty($pkeyPem)) {
                return ['solicitud' => false, 'error' => 'PFX no contiene llave privada.'];
            }

            $pkey = @openssl_pkey_get_private($pkeyPem, $password ?: null);
        } else {
            $pkey = @openssl_pkey_get_private($keyContent, $password ?: null);
        }

        if ($pkey === false) {
            return ['solicitud' => false, 'error' => 'No se pudo obtener la llave privada (contraseña inválida o formato no soportado).'];
        }

        $signature = '';
        $ok = openssl_sign($data, $signature, $pkey, self::algoToOpenSSL($algo));

        if (is_resource($pkey)) {
            openssl_free_key($pkey);
        }

        if (!$ok) {
            return ['solicitud' => false, 'error' => 'Error generando la firma.'];
        }

        return ['solicitud' => true, 'firma' => base64_encode($signature), 'message' => 'Firma generada exitosamente.'];
    }
}

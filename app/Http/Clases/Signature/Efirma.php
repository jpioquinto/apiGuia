<?php

namespace App\Http\Clases\Signature;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Config;
use App\Http\Helpers\CadenaHelper;
use Exception;

class Efirma
{
    public static function getEnv()
    {
        return Config::get('filesystems.default');
    }
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
        try {
            if (!Storage::disk(self::getEnv())->exists($keyPath)) {
                throw new Exception("Archivo de llave no encontrado: {$keyPath}");
            }

            $keyContent = Storage::disk(self::getEnv())->get($keyPath);

            // Try to read as PFX first
            if (preg_match('/\.p12$|\.pfx$/i', $keyPath)) {
                if (!openssl_pkcs12_read($keyContent, $certs, $password)) {
                    throw new Exception('No se pudo leer el archivo PFX/P12 con la contraseña proporcionada.');
                }

                $pkeyPem = $certs['pkey'] ?? null;
                if (empty($pkeyPem)) {
                    throw new Exception('El archivo PFX/P12 no contiene una llave privada.');
                }

                $pkey = @openssl_pkey_get_private($pkeyPem, $password ?: null);
            } else {
                $pkey = @openssl_pkey_get_private($keyContent, $password ?: null);
            }

            if ($pkey === false) {
                throw new Exception('No se pudo obtener la llave privada (contraseña inválida o formato no soportado).');
            }

            $signature = '';
            $ok = openssl_sign($data, $signature, $pkey, self::algoToOpenSSL($algo));

            if (is_resource($pkey)) {
                openssl_free_key($pkey);
            }

            if (!$ok) {
                throw new Exception('Error generando la firma con la llave proporcionada.');
            }

            return ['solicitud' => true, 'firma' => base64_encode($signature), 'message' => 'Firma generada exitosamente.'];

        } catch (\Exception $e) {
            return ['solicitud' => false, 'error' => $e->getMessage()];
        }
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

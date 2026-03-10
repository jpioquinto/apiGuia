<?php

namespace App\Http\Clases\Signature;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Config;
use App\Http\Helpers\CadenaHelper;

class FielValidator
{
    public static function getEnv()
    {
        return Config::get('filesystems.default');
    }
    /**
     * convert a certificate file to PEM format if it's not already, and return the PEM string.
     * Returns cert PEM.
     *
     * @param string $certPath
     * @return string
     */
    public static function certToPem(string $certPath): string
    {
        if (!Storage::disk(self::getEnv())->exists($certPath)) {
            throw new \RuntimeException("Certificado no encontrado: {$certPath}");
        }

        $data = Storage::disk(self::getEnv())->get($certPath);
        if ($data === false) {
            throw new \RuntimeException('No se pudo leer el certificado');
        }

        if (strpos($data, '-----BEGIN CERTIFICATE-----') !== false) {
            return $data;
        }

        $b64 = chunk_split(base64_encode($data), 64, "\n");
        return "-----BEGIN CERTIFICATE-----\n" . $b64 . "-----END CERTIFICATE-----\n";
    }
    /**
     * convert a certificate file to PEM format if it's not already, and return the PEM string.
     * Returns cert PEM.
     *
     * @param string $certPath
     * @return string
     */
    public static function keyToPem(string $keyPath, string $password = ''): string|null
    {
        $pathPEM = CadenaHelper::removeExtension($keyPath) . '.pem';
        $pKey = shell_exec(
            sprintf(
                "openssl pkcs8 -inform DER -in %s -passin pass:%s -out %s",
                Storage::disk(self::getEnv())->path($keyPath), $password,
                Storage::disk(self::getEnv())->path($pathPEM)
                )
            );
        return Storage::disk(self::getEnv())->exists($pathPEM) ? $pathPEM : null;
    }
    /**
     * Validate a certificate and private key (PEM or PFX).
     * Returns an array with solicitud flag and details or error message.
     *
     * @param string $certPath
     * @param string $keyPath
     * @param string $password
     * @return array
     */
    public static function validate(string $certPath, string $keyPath, string $password = ''): array
    {
        try {
            if (!Storage::disk(self::getEnv())->exists($certPath)) {
                throw new \Exception("Certificado no encontrado: $certPath");
            }

            if (!Storage::disk(self::getEnv())->exists($keyPath)) {
                throw new \Exception("Llave no encontrada: $keyPath");
            }

            $certContent = Storage::disk(self::getEnv())->get($certPath);
            $keyContent = Storage::disk(self::getEnv())->get($keyPath);
            #$keyContent = Storage::disk(self::getEnv())->get('temp/signature/00001000000517964209/keyPEM.pem');

            $parsed = [];

            # Handle PFX/P12 files: if one of the files is a PFX, try to read it
            $isPfx = preg_match('/\.p12$|\.pfx$/i', $certPath) || preg_match('/\.p12$|\.pfx$/i', $keyPath);
            if ($isPfx) {
                $p12 = $certContent;
                if (empty($p12)) {
                    $p12 = $keyContent;
                }

                if (!openssl_pkcs12_read($p12, $certs, $password)) {
                    throw new \Exception('No se pudo leer el archivo PFX/P12 con la contraseña proporcionada.');
                }

                $certPem = $certs['cert'] ?? null;
                $pkeyPem = $certs['pkey'] ?? null;
            } else {
                $pemPath = self::keyToPem($keyPath, $password);
                $certPem = self::certToPem($certPath);
                $pkeyPem = $pemPath ? Storage::disk(self::getEnv())->get($pemPath): $keyContent;
            }

            if (empty($certPem) || empty($pkeyPem)) {
                throw new \Exception("Contenido de certificado o llave inválido.");
            }

            $x509 = @openssl_x509_read($certPem);
            if ($x509 === false) {
                throw new \Exception('Certificado inválido o formato no reconocido.');
            }

            $certInfo = openssl_x509_parse($x509);
            if ($certInfo === false) {
                throw new \Exception('No se pudo parsear el certificado.');
            }

            if (self::invalidCertPeriod($certInfo)) {
                throw new \Exception('Período del certificado no válido.');
            }

            if (self::certExpire($certInfo)) {
                throw new \Exception('Certificado expirado.');
            }

            # Check private key can be read (with or without password)
            #$pkey = @openssl_pkey_get_private($keyContent, $password ?: null);
            $pkey = @openssl_pkey_get_private($pkeyPem, 'piokoro-san');
            #$pkey = @openssl_pkey_get_private($keyContent);

            if ($pkey === false) {
                throw new \Exception('No se pudo leer la llave privada con la contraseña proporcionada. :( '.openssl_error_string());
            }

            $pubkey = openssl_pkey_get_public($certPem);

            if (!self::privateKeyMatchesCert($pkey, $pubkey)) {
                if ($pkey) {
                    @openssl_free_key($pkey);
                }
                if ($pubkey) {
                    @openssl_free_key($pubkey);
                }
                throw new \Exception('FIEL No válida.');
            }

            $parsed = [
                'solicitud' => true,
                'message' => 'La FIEL es válida.',
                'subject' => $certInfo['subject'] ?? null,
                'issuer' => $certInfo['issuer'] ?? null,
                'serialNumber' => $certInfo['serialNumberHex'] ?? ($certInfo['serialNumber'] ?? null),
                'validFrom' => $certInfo ? date('c', $certInfo['validFrom_time_t']) : null,
                'validTo' => $certInfo ? date('c', $certInfo['validTo_time_t']) : null,
                'signatureAlgorithm' => $certInfo['signatureTypeSN'] ?? ($certInfo['signatureTypeLN'] ?? null),
            ];

            if ($pkey) {
                openssl_free_key($pkey);
            }

            if ($x509) {
                openssl_x509_free($x509);
            }

            return $parsed;
        } catch (\Exception $e) {
            return ['solicitud' => false, 'error' => $e->getMessage()];
        }
    }

    public static function invalidCertPeriod($certInfo): bool
    {
        $now = time();
        $validFrom = isset($certInfo['validFrom_time_t']) ? $certInfo['validFrom_time_t'] : null;
        return ($validFrom !== null && $now < $validFrom);
    }

    public static function certExpire($certInfo): bool
    {
        $now = time();
        $validTo = isset($certInfo['validTo_time_t']) ? $certInfo['validTo_time_t'] : null;
        return ($validTo !== null && $now > $validTo);
    }

    /**
     * Checks if a private key matches the public key in the certificate.
     */
    public static function privateKeyMatchesCert($priv, $pub): bool
    {
        if ($pub === false || $priv === false) {
            if (is_resource($pub)) openssl_pkey_free($pub);
            if (is_resource($priv)) openssl_pkey_free($priv);
            return false;
        }

        $pubDetails = openssl_pkey_get_details($pub);
        $privDetails = openssl_pkey_get_details($priv);

        $matches = false;
        if ($pubDetails && $privDetails && isset($pubDetails['rsa']['n']) && isset($privDetails['rsa']['n'])) {
            $matches = ($pubDetails['rsa']['n'] === $privDetails['rsa']['n']);
        }

        if (is_resource($pub)) openssl_pkey_free($pub);
        if (is_resource($priv)) openssl_pkey_free($priv);

        return $matches;
    }
}

<?php

namespace App\Http\Clases\Signature;

class FielValidator
{
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
            if (!file_exists($certPath)) {
                throw new \Exception("Certificado no encontrado: $certPath");
            }

            if (!file_exists($keyPath)) {
                throw new \Exception("Llave no encontrada: $keyPath");
            }

            $certContent = file_get_contents($certPath);
            $keyContent = file_get_contents($keyPath);

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
                $certPem = $certContent;
                $pkeyPem = $keyContent;
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
            $pkey = @openssl_pkey_get_private($pkeyPem, $password ?: null);
            if ($pkey === false) {
                throw new \Exception('No se pudo leer la llave privada con la contraseña proporcionada.');
            }

            $pubkey = openssl_pkey_get_public($certPem);

            if (!self::privateKeyMatchesCert($pkey, $pubkey)) {
                throw new \Exception('FIEL No válida.');
            }

            $parsed = [
                'solicitud' => true,
                'message' => 'La FIEL es válida.',
                'subject' => $certInfo['subject'] ?? null,
                'issuer' => $certInfo['issuer'] ?? null,
                'serialNumber' => $certInfo['serialNumberHex'] ?? ($certInfo['serialNumber'] ?? null),
                'validFrom' => $validFrom ? date('c', $validFrom) : null,
                'validTo' => $validTo ? date('c', $validTo) : null,
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

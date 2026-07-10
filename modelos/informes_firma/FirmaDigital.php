<?php

require_once __DIR__ . '/FirmaDigitalInterfaz.php';

class FirmaDigital implements FirmaDigitalInterface
{
    private string $privateKeyPath;
    private string $publicKeyPath;

    public function __construct()
    {
        $this->privateKeyPath = __DIR__ . '/../../seguridad/Firmas/Privatekey.pem';
        $this->publicKeyPath  = __DIR__ . '/../../seguridad/Firmas/PublicKey.pem';
    }

    public function generarHash($contenido)
    {
        return hash('sha256', $contenido);
    }

    public function firmarHash($hash)
    {
        if (!file_exists($this->privateKeyPath) || filesize($this->privateKeyPath) === 0) {
            die("Error: La llave privada no existe o está vacía.");
        }

        $privateKey = file_get_contents($this->privateKeyPath);

        $resultado = openssl_sign($hash, $firma, $privateKey, OPENSSL_ALGO_SHA256);

        if (!$resultado) {
            die("Error: No se pudo firmar el hash.");
        }

        return base64_encode($firma);
    }

    public function verificarFirma($hash, $firma)
    {
        if (!file_exists($this->publicKeyPath) || filesize($this->publicKeyPath) === 0) {
            return false;
        }

        $publicKey = file_get_contents($this->publicKeyPath);
        $firmaDecodificada = base64_decode($firma);

        return openssl_verify($hash, $firmaDecodificada, $publicKey, OPENSSL_ALGO_SHA256) === 1;
    }
}
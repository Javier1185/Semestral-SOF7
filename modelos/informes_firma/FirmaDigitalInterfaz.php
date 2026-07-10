<?php

interface FirmaDigitalInterface {
    public function generarHash($contenido);
    public function firmarHash($hash);
    public function verificarFirma($hash, $firma);
}
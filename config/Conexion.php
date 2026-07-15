<?php

class Conexion
{
    private static ?Conexion $instancia = null;
    private PDO $pdo;

    private string $host = '127.0.0.1';
    private string $puerto = '3307';
    private string $baseDatos = 'sistema_contable';
    private string $usuario = 'root';
    private string $contrasena = '';
    private string $charset = 'utf8mb4';

    private function __construct()
    {
        $dsn = "mysql:host={$this->host};port={$this->puerto};dbname={$this->baseDatos};charset={$this->charset}";

        $opciones = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        try {
            $this->pdo = new PDO($dsn, $this->usuario, $this->contrasena, $opciones);
        } catch (PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }

    public static function obtenerInstancia(): Conexion
    {
        if (self::$instancia === null) {
            self::$instancia = new Conexion();
        }

        return self::$instancia;
    }

    public function obtenerPDO(): PDO
    {
        return $this->pdo;
    }

    private function __clone() {}

    public function __wakeup()
    {
        throw new Exception('No se puede deserializar un singleton.');
    }
}
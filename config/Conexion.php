<?php

class Conexion
{
    private static ?Conexion $instancia = null;
    private PDO $pdo;

    // Datos de conexion. Ajusta segun tu entorno (XAMPP por defecto).
    private string $host = 'localhost';
    private string $baseDatos = 'sistema_contable';
    private string $usuario = 'root';
    private string $contrasena = '';
    private string $charset = 'utf8mb4';

    // El constructor es privado: nadie puede hacer "new Conexion()" desde afuera.
    private function __construct()
    {
        $dsn = "mysql:host={$this->host};dbname={$this->baseDatos};charset={$this->charset}";

        $opciones = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->pdo = new PDO($dsn, $this->usuario, $this->contrasena, $opciones);
        } catch (PDOException $e) {
            // No exponemos el error real al usuario final, solo lo registramos.
            error_log('Error de conexion a la base de datos: ' . $e->getMessage());
            die('No se pudo conectar a la base de datos.');
        }
    }

    // Punto de acceso unico a la instancia de Conexion.
    public static function obtenerInstancia(): Conexion
    {
        if (self::$instancia === null) {
            self::$instancia = new Conexion();
        }

        return self::$instancia;
    }
    
    // Devuelve el objeto PDO listo para usarse en los modelos.
    public function obtenerPDO(): PDO
    {
        return $this->pdo;
    }

    // Evita que la instancia se pueda clonar o deserializar (rompería el singleton).
    private function __clone() {}

    public function __wakeup()
    {
        throw new Exception('No se puede deserializar un singleton.');
    }
}
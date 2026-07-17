# Sistema Financiero

Sistema web para la administración de información financiera de una organización, desarrollado en PHP bajo el patrón **MVC**, con control de acceso basado en roles y una bitácora de auditoría.

**Universidad Tecnológica de Panamá** — Facultad de Ingeniería de Sistemas Computacionales
Departamento de Desarrollo de Software — Lic. en Desarrollo y Gestión de Software
**Materia:** Desarrollo de Software 7 · **Profesora:** Irina Fong

## Integrantes

- Javier Forchiney — 8-1025-1467
- Pablo Pimentel — 8-1012-100
- Marcos De los Santos — 8-961-2283
- Mayke Saucedo — 8-1026-920

## Descripción

El sistema centraliza el registro y control de las operaciones contables de una organización, permitiendo:

- Autenticación segura de usuarios
- Administración de usuarios, roles y permisos por módulo
- Catálogo de cuentas contables
- Registro de asientos contables en el Diario General (encabezado + detalle)
- Generación de informes financieros (Estado de Resultados, Balance General)
- Bitácora de auditoría de todas las acciones del sistema

## Requisitos Funcionales

| Código | Requerimiento |
|--------|---------------|
| RF-01 | Inicio de sesión |
| RF-02 | Administrar usuarios |
| RF-03 | Modificar usuarios existentes |
| RF-04 | Activar/inactivar usuarios |
| RF-05 | Administrar roles |
| RF-06 | Asignar permisos por módulo |
| RF-07 | Administrar catálogo de cuentas |
| RF-08 | Registrar transacciones en el Diario General |
| RF-09 | Registrar acciones en bitácora |
| RF-10 | Generar informes financieros |

## Requisitos No Funcionales

- Programación orientada a objetos
- Arquitectura MVC
- Acceso a datos mediante PDO (consultas preparadas)
- Validación y sanitización de datos de entrada
- Control de acceso mediante sesiones
- Compatibilidad con navegadores modernos
- Interfaz intuitiva

## Requisitos de Instalación

**Software necesario:**

- PHP 8.0 o superior
- MySQL / MariaDB 8.0+
- Servidor Apache (recomendado: XAMPP)
- Extensión `pdo_mysql` habilitada
- Navegador web moderno (Chrome, Firefox, Edge)

**Pasos para instalar el proyecto:**

1. Clonar el repositorio dentro de la carpeta `htdocs` de XAMPP:
   ```
   git clone https://github.com/Javier1185/Semestral-SOF7.git
   ```
2. Iniciar Apache y MySQL desde el panel de control de XAMPP.
3. Crear una base de datos en phpMyAdmin (ej. `sistema_contable`).
4. Importar el script SQL ubicado en `database/sistema_contable.sql` (ver sección *Base de Datos* abajo).
5. Configurar las credenciales de conexión en el archivo `Conexion.php` (host, usuario, contraseña, nombre de la base de datos).
6. Acceder al sistema desde el navegador:
   ```
   http://localhost/Semestral-SOF7/
   ```
7. Iniciar sesión con un usuario administrador previamente registrado en la base de datos.

## Base de Datos

Motor: **MySQL**, acceso mediante **PDO**.

| Tabla | Función |
|-------|---------|
| usuarios | Información de los usuarios del sistema |
| roles | Define los roles disponibles |
| permisos | Permisos asignados a cada rol por módulo |
| bitacora | Registro de auditoría de acciones |
| cuentas | Catálogo de cuentas contables |
| diario | Encabezados de asientos contables |
| diario_detalle | Detalle de cada asiento contable |
| cierres | Cierres de informes financieros (con hash y firma digital) |

## Arquitectura

El sistema sigue el patrón **Modelo-Vista-Controlador (MVC)**:

| Capa | Responsabilidad | Ejemplos |
|------|------------------|----------|
| Modelo | Comunicación con la BD y reglas de negocio | `Conexion.php`, `Validaciones.php`, `Bitacora.php` |
| Vista | Interfaces de usuario | `usuarios_index.php`, `roles_index.php`, `permisos_index.php` |
| Controlador | Comunicación entre vista y modelo | `LoginController.php`, `LogoutController.php`, `InformeController.php` |

## Tecnologías Utilizadas

- PHP
- MySQL
- HTML5 / CSS3
- JavaScript
- TCPDF3
- PDO
- Apache (XAMPP)
- Git / GitHub

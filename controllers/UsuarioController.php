<?php
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../config/database.php';

class UsuarioController {
    private $usuario;
    private $db;

    public function __construct() {
        $this->db = new Database();
        $this->usuario = new Usuario($this->db->getConnection());
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            if (!empty($email) && !empty($password)) {
                $usuario = $this->usuario->login($email, $password);
                if ($usuario) {
                    // Guardar datos en sesión
                    $_SESSION['id_usuario'] = $usuario->id;
                    $_SESSION['nombre'] = $usuario->name;
                    $_SESSION['rol_usuario'] = $usuario->role;
                    
                    header('Location: ?route=dashboard');
                    exit();
                } else {
                    // Verificar si el email existe en la base de datos
                    $stmt = $this->db->getConnection()->prepare("SELECT 1 FROM usuarios WHERE email = ?");
                    $stmt->bind_param("s", $email);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    if ($result->num_rows === 0) {
                        header('Location: ?route=login&error=Email no encontrado');
                    } else {
                        // Verificar si la cuenta está activa
                        $stmt = $this->db->getConnection()->prepare("SELECT activo FROM usuarios WHERE email = ?");
                        $stmt->bind_param("s", $email);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $row = $result->fetch_assoc();
                        
                        if ($row['activo'] != 1) {
                            header('Location: ?route=login&error=Cuenta inactiva');
                        } else {
                            header('Location: ?route=login&error=Contraseña incorrecta');
                        }
                    }
                    exit();
                }
            } else {
                header('Location: ?route=login&error=Todos los campos son requeridos');
                exit();
            }
        }
        
        // Mostrar la vista de login
        include __DIR__ . '/../views/login/index.php';
    }

    public function registro() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = $_POST['nombre'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            if (!empty($nombre) && !empty($email) && !empty($password)) {
                if ($this->usuario->crearUsuario($email, $password, $nombre)) {
                    header('Location: ?route=login&success=Registro exitoso');
                    exit();
                } else {
                    header('Location: ?route=registro&error=Error al crear usuario');
                    exit();
                }
            } else {
                header('Location: ?route=registro&error=Todos los campos son requeridos');
                exit();
            }
        }
        
        // Mostrar la vista de registro
        include __DIR__ . '/../views/login/registro.php';
    }

    public function logout() {
        // Limpiar todas las variables de sesión
        $_SESSION = array();
        
        // Si se desea eliminar la cookie de sesión, descomenta la siguiente línea
        // if (ini_get("session.use_cookies")) {
        //     $params = session_get_cookie_params();
        //     setcookie(session_name(), '', time() - 42000,
        //         $params["path"], $params["domain"],
        //         $params["secure"], $params["httponly"]
        //     );
        // }
        
        // Destruir la sesión
        session_destroy();
        
        // Redirigir a la página de login con mensaje de éxito
        header('Location: ?route=login&success=Has cerrado sesión correctamente');
        exit();
    }

    public function isLogged() {
        return isset($_SESSION['id_usuario']);
    }

    public function getUsuarioActual() {
        if ($this->isLogged()) {
            return [
                'id' => $_SESSION['id_usuario'],
                'nombre' => $_SESSION['nombre'],
                'rol' => $_SESSION['rol_usuario']
            ];
        }
        return null;
    }
}

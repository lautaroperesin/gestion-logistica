<?php
class Usuario {
    private $conn;

    public function __construct($conexion) {
        $this->conn = $conexion;
    }

    public function login($email, $password) {
        try {
            // Verificar si el email existe
            $stmt = $this->conn->prepare("SELECT id_usuario, nombre, email, password_hash, rol_usuario, activo FROM usuarios WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 1) {
                $row = $result->fetch_assoc();
                
                // Verificar si la cuenta está activa
                if ($row['activo'] != 1) {
                    throw new Exception('Cuenta inactiva');
                }
                
                // Mostrar información de depuración
                error_log("Debug login: ");
                error_log("Email: " . $email);
                error_log("Password: " . $password);
                error_log("Stored hash: " . $row['password_hash']);
                error_log("Verification: " . (password_verify($password, $row['password_hash']) ? 'true' : 'false'));
                
                // Verificar la contraseña
                if (!password_verify($password, $row['password_hash'])) {
                    throw new Exception('Contraseña incorrecta');
                }
                
                // Actualizar último acceso
                $this->conn->query("UPDATE usuarios SET ultimo_acceso = NOW() WHERE id_usuario = " . $row['id_usuario']);
                
                return (object) [
                    'id' => $row['id_usuario'],
                    'name' => $row['nombre'],
                    'email' => $row['email'],
                    'role' => $row['rol_usuario']
                ];
            } else {
                throw new Exception('Email no encontrado');
            }
        } catch (Exception $e) {
            error_log('Error en login: ' . $e->getMessage());
            return null;
        }
    }

    public function crearUsuario($email, $password, $nombre, $rol = 'usuario') {
        try {
            // Verificar si el email ya existe
            $stmt = $this->conn->prepare("SELECT 1 FROM usuarios WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                throw new Exception('Email ya registrado');
            }
            
            // Hashear la contraseña
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            
            // Insertar el nuevo usuario
            $stmt = $this->conn->prepare("INSERT INTO usuarios (email, password_hash, nombre, rol_usuario, activo, fecha_alta) VALUES (?, ?, ?, ?, 1, NOW())");
            $stmt->bind_param("ssss", $email, $password_hash, $nombre, $rol);
            
            return $stmt->execute();
        } catch (Exception $e) {
            error_log('Error al crear usuario: ' . $e->getMessage());
            return false;
        }
    }
}

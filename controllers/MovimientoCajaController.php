<?php
require_once __DIR__ . '/../models/MovimientoCaja.php';
require_once __DIR__ . '/../config/database.php';

class MovimientoCajaController {
    private $movimientoCajaModel;
    private $db;

    public function __construct() {
        $this->db = new Database();
        $this->movimientoCajaModel = new MovimientoCaja($this->db->getConnection());
    }

    public function index() {
        $porPagina = 10;
        $pagina = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        
        // Obtener parámetros de búsqueda
        $filtros = [
            'numero_factura' => $_GET['numero_factura'] ?? '',
            'cliente' => $_GET['cliente'] ?? '',
            'fecha_desde' => $_GET['fecha_desde'] ?? '',
            'fecha_hasta' => $_GET['fecha_hasta'] ?? ''
        ];
        
        // Obtener el total de movimientos para la paginación
        $totalMovimientos = $this->movimientoCajaModel->contarTotal($filtros);
        $totalPaginas = ceil($totalMovimientos / $porPagina);
        
        // Asegurarse de que la página actual esté dentro del rango válido
        $pagina = max(1, min($pagina, $totalPaginas));
        
        // Obtener los movimientos para la página actual
        $movimientos = $this->movimientoCajaModel->obtenerTodos($porPagina, $pagina, $filtros);
        
        include __DIR__ . '/../views/layouts/header.php';
        include __DIR__ . '/../views/movimientos_caja/index.php';
        include __DIR__ . '/../views/layouts/footer.php';
    }

    public function create() {
        $facturas = (new Factura($this->db->getConnection()))->obtenerTodos();
        $metodos_pago = (new MetodoPago($this->db->getConnection()))->obtenerTodos();
        include __DIR__ . '/../views/layouts/header.php';
        include __DIR__ . '/../views/movimientos_caja/form.php';
        include __DIR__ . '/../views/layouts/footer.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_factura = $_POST['id_factura'] ?? null;
            $fecha_pago = $_POST['fecha_pago'] ?? date('Y-m-d H:i:s');
            
            // Verificar si se están enviando múltiples métodos de pago (desde pago.php)
            if (isset($_POST['id_metodo_pago']) && is_array($_POST['id_metodo_pago'])) {
                // Obtener el saldo pendiente de la factura
                require_once __DIR__ . '/../models/Factura.php';
                $facturaModel = new Factura($this->db->getConnection());
                $factura = $facturaModel->obtenerPorId($id_factura);
                
                if (!$factura) {
                    header('Location: ?route=facturas&error=Factura no encontrada');
                    exit;
                }
                
                $saldo_pendiente = floatval($factura['saldo_pendiente'] ?? 0);
                
                // Procesar múltiples métodos de pago
                $id_metodos_pago = $_POST['id_metodo_pago'] ?? [];
                $montos_metodo = $_POST['monto_metodo'] ?? [];
                $observaciones_metodo = $_POST['observaciones_metodo'] ?? [];
                
                $errores = [];
                $exitosos = 0;
                
                // Validar que todos los arrays tengan la misma longitud
                $total = count($id_metodos_pago);
                if (count($montos_metodo) !== $total || count($observaciones_metodo) !== $total) {
                    header('Location: ?route=facturas_pago&id_factura=' . $id_factura . '&error=Error en los datos enviados');
                    exit;
                }
                
                // Calcular el total a pagar antes de procesar
                $total_a_pagar = 0;
                $montos_validos = [];
                
                for ($i = 0; $i < $total; $i++) {
                    $id_metodo_pago = $id_metodos_pago[$i] ?? null;
                    $monto = floatval($montos_metodo[$i] ?? 0);
                    
                    // Validar que el método de pago y el monto sean válidos
                    if (!empty($id_metodo_pago) && $monto > 0) {
                        $total_a_pagar += $monto;
                        $montos_validos[] = [
                            'id_metodo_pago' => $id_metodo_pago,
                            'monto' => $monto,
                            'observaciones' => trim($observaciones_metodo[$i] ?? ''),
                            'index' => $i
                        ];
                    }
                }
                
                // Validar que el total no exceda el saldo pendiente
                if ($total_a_pagar > $saldo_pendiente) {
                    header('Location: ?route=facturas_pago&id_factura=' . $id_factura . '&error=' . urlencode('El total a pagar ($' . number_format($total_a_pagar, 2) . ') excede el saldo pendiente ($' . number_format($saldo_pendiente, 2) . ')'));
                    exit;
                }
                
                // Validar que haya al menos un monto válido
                if (empty($montos_validos)) {
                    header('Location: ?route=facturas_pago&id_factura=' . $id_factura . '&error=Debe ingresar al menos un monto válido mayor a 0');
                    exit;
                }
                
                // Procesar cada método de pago válido
                foreach ($montos_validos as $item) {
                    // Crear el movimiento de caja
                    if ($this->movimientoCajaModel->crear($id_factura, $item['id_metodo_pago'], $fecha_pago, $item['monto'], $item['observaciones'])) {
                        $exitosos++;
                    } else {
                        $errores[] = "Error al registrar el pago con método de pago #" . ($item['index'] + 1);
                    }
                }
                
                if ($exitosos > 0) {
                    // Redirigir a la página de pago de la factura para ver el resultado
                    if (count($errores) > 0) {
                        header('Location: ?route=facturas_pago&id_factura=' . $id_factura . '&error=' . urlencode(implode(', ', $errores)));
                    } else {
                        header('Location: ?route=facturas_pago&id_factura=' . $id_factura . '&success=Pago registrado correctamente');
                    }
                    exit;
                } else {
                    header('Location: ?route=facturas_pago&id_factura=' . $id_factura . '&error=No se pudo registrar ningún pago');
                    exit;
                }
            } else {
                // Procesar un solo método de pago (desde form.php tradicional)
                $id_metodo_pago = $_POST['id_metodo_pago'] ?? null;
                $monto = floatval($_POST['monto'] ?? 0);
                $observaciones = $_POST['observaciones'] ?? '';

                if ($this->movimientoCajaModel->crear($id_factura, $id_metodo_pago, $fecha_pago, $monto, $observaciones)) {
                    header('Location: ?route=movimientos_caja');
                    exit;
                } else {
                    header('Location: ?route=movimientos_caja&error=Error al registrar el movimiento');
                    exit;
                }
            }
        }
    }

    public function edit() {
        $id = $_GET['id_movimiento'] ?? null;
        if ($id) {
            $movimiento = $this->movimientoCajaModel->obtenerPorId($id);
            if ($movimiento) {
                $facturas = (new Factura($this->db->getConnection()))->obtenerTodos();
                $metodos_pago = (new MetodoPago($this->db->getConnection()))->obtenerTodos();
                include __DIR__ . '/../views/layouts/header.php';
                include __DIR__ . '/../views/movimientos_caja/form.php';
                include __DIR__ . '/../views/layouts/footer.php';
            } else {
                header('Location: ?route=movimientos_caja&error=Movimiento no encontrado');
                exit;
            }
        } else {
            header('Location: ?route=movimientos_caja&error=ID no válido');
            exit;
        }
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_movimiento = $_POST['id_movimiento'] ?? null;
            $id_factura = $_POST['id_factura'] ?? null;
            $id_metodo_pago = $_POST['id_metodo_pago'] ?? null;
            $fecha_pago = $_POST['fecha_pago'] ?? date('Y-m-d H:i:s');
            $monto = $_POST['monto'] ?? 0;
            $observaciones = $_POST['observaciones'] ?? '';

            if ($this->movimientoCajaModel->editar($id_movimiento, $id_factura, $id_metodo_pago, $fecha_pago, $monto, $observaciones)) {
                header('Location: ?route=movimientos_caja');
                exit;
            }
        }
    }

    public function delete() {
        $id = $_GET['id_movimiento'] ?? null;
        if ($this->movimientoCajaModel->eliminar($id)) {
            header('Location: ?route=movimientos_caja');
            exit;
        }
    }

    public function getByFactura($id_factura) {
        $movimientos = $this->movimientoCajaModel->obtenerPorFactura($id_factura);
        include __DIR__ . '/../views/movimientos_caja/index.php';
    }

    public function exportarReciboPdf() {
        $id_movimiento = $_GET['id_movimiento'] ?? null;
        if (!$id_movimiento) {
            header('Location: ?route=movimientos_caja&error=ID de movimiento no válido');
            exit;
        }

        // Obtener los datos del movimiento de caja
        $movimiento = $this->movimientoCajaModel->obtenerPorId($id_movimiento);
        if (!$movimiento) {
            header('Location: ?route=movimientos_caja&error=Movimiento no encontrado');
            exit;
        }

        // Obtener datos de la factura asociada
        require_once __DIR__ . '/../models/Factura.php';
        $facturaModel = new Factura($this->db->getConnection());
        $factura = $facturaModel->obtenerPorId($movimiento['id_factura']);

        // Obtener datos del cliente
        require_once __DIR__ . '/../models/Cliente.php';
        $clienteModel = new Cliente($this->db->getConnection());
        $cliente = $clienteModel->obtenerPorId($factura['id_cliente']);

        // Obtener datos de la empresa desde la tabla config
        $stmtConfig = $this->db->getConnection()->prepare("SELECT nombre_empresa, telefono_empresa, ubicacion_empresa, email_empresa FROM config LIMIT 1");
        $stmtConfig->execute();
        $config = $stmtConfig->get_result()->fetch_assoc();
        
        // Valores por defecto si no hay configuración
        $nombre_empresa = $config['nombre_empresa'] ?? 'Sistema de Gestión Logística';
        $telefono_empresa = $config['telefono_empresa'] ?? '';
        $ubicacion_empresa = $config['ubicacion_empresa'] ?? '';
        $email_empresa = $config['email_empresa'] ?? '';

        // Incluir la biblioteca TCPDF
        require_once __DIR__ . '/../vendor/tecnickcom/tcpdf/tcpdf.php';

        // Crear una nueva instancia de TCPDF
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // Configuración del documento
        $pdf->SetCreator($nombre_empresa);
        $pdf->SetAuthor($nombre_empresa);
        $pdf->SetTitle('Comprobante de Pago #' . $movimiento['id_movimiento']);
        $pdf->SetSubject('Comprobante de Pago');
        $pdf->SetKeywords('Comprobante, Pago, PDF');

        // Eliminar cabecera y pie de página por defecto
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // Agregar una página
        $pdf->AddPage();

        // Contenido del PDF
        $html = '<!-- CSS -->
        <style>
            body { font-family: helvetica; font-size: 10pt; }
            .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 15px; }
            .header h1 { font-size: 20pt; margin: 0; color: #333; }
            .header .company-name { font-size: 14pt; font-weight: bold; margin: 10px 0 5px 0; color: #28a745; }
            .header p { margin: 3px 0; font-size: 9pt; }
            .receipt-title { text-align: center; margin: 20px 0; background-color: #28a745; color: white; padding: 15px; border-radius: 5px; }
            .receipt-title h1 { font-size: 18pt; margin: 0; }
            .info-section { margin: 20px 0; }
            .info-box { border: 1px solid #ddd; padding: 15px; background-color: #f9f9f9; margin-bottom: 15px; }
            .info-box h3 { margin: 0 0 10px 0; font-size: 11pt; color: #333; border-bottom: 1px solid #ddd; padding-bottom: 5px; }
            .info-box p { margin: 5px 0; font-size: 9pt; }
            .payment-details { border: 2px solid #28a745; padding: 20px; background-color: #f0fff4; margin: 20px 0; }
            .payment-details h2 { margin: 0 0 15px 0; font-size: 13pt; color: #28a745; text-align: center; }
            .payment-row { margin: 10px 0; padding: 8px; border-bottom: 1px solid #ddd; }
            .payment-row:last-child { border-bottom: none; }
            .payment-label { font-weight: bold; display: inline-block; width: 40%; }
            .payment-value { display: inline-block; width: 55%; text-align: right; }
            .amount-paid { background-color: #28a745; color: white; padding: 15px; text-align: center; font-size: 16pt; font-weight: bold; margin: 20px 0; border-radius: 5px; }
            .footer { margin-top: 30px; font-size: 8pt; text-align: center; color: #666; border-top: 1px solid #ddd; padding-top: 10px; }
            .signature-section { margin-top: 40px; }
            .signature-line { border-top: 1px solid #333; width: 200px; margin: 50px auto 5px auto; }
            .signature-text { text-align: center; font-size: 9pt; }
        </style>
        
        <!-- Encabezado -->
        <div class="header">
           <h1>COMPROBANTE DE PAGO</h1>
            <p style="margin: 5px 0; font-size: 10pt;">N° ' . str_pad($movimiento['id_movimiento'], 6, '0', STR_PAD_LEFT) . '</p>
        </div>
        
        <!-- Información del cliente -->
        <div class="info-section">
            <div class="info-box">
                <h3>Datos del Cliente</h3>
                <p><strong>Nombre:</strong> ' . htmlspecialchars($movimiento['cliente']) . '</p>
                ' . (isset($cliente['telefono']) && $cliente['telefono'] ? '<p><strong>Teléfono:</strong> ' . htmlspecialchars($cliente['telefono']) . '</p>' : '') . '
                ' . (isset($cliente['email']) && $cliente['email'] ? '<p><strong>Email:</strong> ' . htmlspecialchars($cliente['email']) . '</p>' : '') . '
            </div>
        </div>
        
        <!-- Detalles del pago -->
        <div class="payment-details">
            <h2>DETALLES DEL PAGO</h2>
            <div class="payment-row">
                <span class="payment-label">Factura N°:</span>
                <span class="payment-value">' . htmlspecialchars($movimiento['numero_factura']) . '</span>
            </div>
            <div class="payment-row">
                <span class="payment-label">Fecha de Pago:</span>
                <span class="payment-value">' . date('d/m/Y H:i', strtotime($movimiento['fecha_pago'])) . '</span>
            </div>
            <div class="payment-row">
                <span class="payment-label">Método de Pago:</span>
                <span class="payment-value">' . htmlspecialchars($movimiento['metodo_pago']) . '</span>
            </div>
            ' . ($movimiento['observaciones'] ? '<div class="payment-row">
                <span class="payment-label">Observaciones:</span>
                <span class="payment-value">' . htmlspecialchars($movimiento['observaciones']) . '</span>
            </div>' : '') . '
        </div>
        
        <!-- Monto pagado -->
        <div class="amount-paid">
            MONTO PAGADO: $' . number_format($movimiento['monto'], 2) . '
        </div>
        
        <!-- Información adicional de la factura -->
        <div class="info-box">
            <h3>Información de la Factura</h3>
            <p><strong>Total de la Factura:</strong> $' . number_format($factura['total'], 2) . '</p>
            <p><strong>Fecha de Emisión:</strong> ' . date('d/m/Y', strtotime($factura['fecha_emision'])) . '</p>
            ' . ($factura['fecha_vencimiento'] ? '<p><strong>Fecha de Vencimiento:</strong> ' . date('d/m/Y', strtotime($factura['fecha_vencimiento'])) . '</p>' : '') . '
        </div>
        

        <!-- Pie de página -->
        <div class="footer">
            <p>Gracias por su pago - ' . htmlspecialchars($nombre_empresa) . '</p>
            ' . ($ubicacion_empresa ? '<p><strong>Dirección:</strong> ' . htmlspecialchars($ubicacion_empresa) . '</p>' : '') . '
            ' . ($telefono_empresa ? '<p><strong>Teléfono:</strong> ' . htmlspecialchars($telefono_empresa) . '</p>' : '') . '
            ' . ($email_empresa ? '<p><strong>Email:</strong> ' . htmlspecialchars($email_empresa) . '</p>' : '') . '
        </div>';

        // Escribir el contenido HTML
        $pdf->writeHTML($html, true, false, true, false, '');

        // Cerrar y generar el PDF
        $pdf->Output('recibo_pago_' . str_pad($movimiento['id_movimiento'], 6, '0', STR_PAD_LEFT) . '.pdf', 'I');
        exit;
    }
}

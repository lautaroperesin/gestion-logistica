<?php
require_once __DIR__ . '/../models/Factura.php';
require_once __DIR__ . '/../config/database.php';

class FacturaController {
    private $facturaModel;
    private $db;

    public function __construct() {
        $this->db = new Database();
        $this->facturaModel = new Factura($this->db->getConnection());
    }

    public function index() {
        // Obtener parámetros de filtrado
        $filtros = [
            'numero_factura' => $_GET['numero_factura'] ?? '',
            'id_cliente' => $_GET['id_cliente'] ?? '',
            'estado' => $_GET['estado'] ?? '',
            'fecha_desde' => $_GET['fecha_desde'] ?? '',
            'fecha_hasta' => $_GET['fecha_hasta'] ?? ''
        ];
        
        // Configuración de paginación
        $porPagina = 10; // Fijamos 10 elementos por página
        $pagina = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        
        // Obtener datos con paginación
        $resultado = $this->facturaModel->buscar($filtros, $porPagina, $pagina);
        
        // Extraer datos y paginación
        $facturas = $resultado['datos'];
        $paginacion = $resultado['paginacion'];
        
        // Obtener lista de clientes para el filtro
        $clientes = (new Cliente($this->db->getConnection()))->obtenerTodos(100);
        
        // Pasar datos a la vista
        include __DIR__ . '/../views/layouts/header.php';
        include __DIR__ . '/../views/facturas/index.php';
        include __DIR__ . '/../views/layouts/footer.php';
    }

    public function create() {
        $envios = (new Envio($this->db->getConnection()))->obtenerTodos(100);
        $clientes = (new Cliente($this->db->getConnection()))->obtenerTodos(100);
        include __DIR__ . '/../views/layouts/header.php';
        include __DIR__ . '/../views/facturas/form.php';
        include __DIR__ . '/../views/layouts/footer.php';
    }

    public function createFromEnvio($idEnvio) {
        $envioModel = new Envio($this->db->getConnection());
        $clienteModel = new Cliente($this->db->getConnection());
        
        // Obtener datos del envío
        $envio = $envioModel->obtenerPorId($idEnvio);
        
        if (!$envio) {
            $_SESSION['error'] = 'El envío especificado no existe';
            header('Location: ?route=envios');
            exit();
        }
        
        // Obtener datos del cliente
        $cliente = $clienteModel->obtenerPorId($envio['id_cliente']);
        
        // Generar número de factura único
        $numeroFactura = 'FAC-' . date('Ymd') . '-' . $envio['id_envio'];
        
        // Preparar datos para la factura
        $factura = [
            'id_factura' => null, // Asegurar que es nueva factura
            'numero_factura' => $numeroFactura,
            'id_envio' => $envio['id_envio'],
            'id_cliente' => $envio['id_cliente'],
            'cliente' => $cliente['cliente'],
            'fecha_emision' => date('Y-m-d'),
            'fecha_vencimiento' => date('Y-m-d', strtotime('+30 days')),
            'subtotal' => $envio['costo_total'],
            'impuestos' => $envio['costo_total'] * 0.21, // 21% de impuestos
            'total' => $envio['costo_total'] * 1.21,
            'estado' => '1', // 1 = Emitida
            'concepto' => 'Transporte de envío #' . $envio['numero_seguimiento']
        ];
        
        $clientes = $clienteModel->obtenerTodos(100);
        $envios = (new Envio($this->db->getConnection()))->obtenerTodos();
        $fromEnvio = true; // Flag to indicate this form was loaded from a shipment
        
        include __DIR__ . '/../views/layouts/header.php';
        include __DIR__ . '/../views/facturas/form.php';
        include __DIR__ . '/../views/layouts/footer.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_envio = $_POST['id_envio'] ?? null;
            $numero_factura = $_POST['numero_factura'] ?? '';
            $fecha_emision = $_POST['fecha_emision'] ?? date('Y-m-d H:i:s');
            $fecha_vencimiento = $_POST['fecha_vencimiento'] ?? null;
            $id_cliente = $_POST['id_cliente'] ?? null;
            $iva = $_POST['iva'] ?? 0;
            $subtotal = $_POST['subtotal'] ?? 0;
            $total = $_POST['total'] ?? 0;
            $estado = $_POST['estado'] ?? 1;

            if ($this->facturaModel->crear($id_envio, $numero_factura, $fecha_emision, $fecha_vencimiento, $id_cliente, $iva, $subtotal, $total, $estado)) {
                header('Location: ?route=facturas');
                exit;
            }
        }
    }

    public function edit() {
        $id = $_GET['id_factura'] ?? null;
        if ($id) {
            $factura = $this->facturaModel->obtenerPorId($id);
            if ($factura) {
                $envios = (new Envio($this->db->getConnection()))->obtenerTodos(100);
                $clientes = (new Cliente($this->db->getConnection()))->obtenerTodos(100);
                include __DIR__ . '/../views/layouts/header.php';
                include __DIR__ . '/../views/facturas/form.php';
                include __DIR__ . '/../views/layouts/footer.php';
            } else {
                header('Location: ?route=facturas&error=Factura no encontrada');
                exit;
            }
        } else {
            header('Location: ?route=facturas&error=ID de factura no válido');
            exit;
        }
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_factura = $_POST['id_factura'] ?? null;
            $id_envio = $_POST['id_envio'] ?? null;
            $numero_factura = $_POST['numero_factura'] ?? '';
            $fecha_emision = $_POST['fecha_emision'] ?? date('Y-m-d H:i:s');
            $fecha_vencimiento = $_POST['fecha_vencimiento'] ?? null;
            $id_cliente = $_POST['id_cliente'] ?? null;
            $iva = $_POST['iva'] ?? 0;
            $subtotal = $_POST['subtotal'] ?? 0;
            $total = $_POST['total'] ?? 0;
            $estado = $_POST['estado'] ?? 1;

            if ($this->facturaModel->editar($id_factura, $id_envio, $numero_factura, $fecha_emision, $fecha_vencimiento, $id_cliente, $iva, $subtotal, $total, $estado)) {
                header('Location: ?route=facturas');
                exit;
            }
        }
    }

    public function delete() {
        // Obtener el ID de la factura de GET o POST
        $id = $_POST['id_factura'] ?? $_GET['id_factura'] ?? null;
        
        if ($id && $this->facturaModel->eliminar($id)) {
            // Redirigir con mensaje de éxito
            header('Location: ?route=facturas&success=Factura eliminada correctamente');
            exit;
        } else {
            // Redirigir con mensaje de error
            header('Location: ?route=facturas&error=No se pudo eliminar la factura');
            exit;
        }
    }

    public function pago() {
        $id_factura = $_GET['id_factura'] ?? null;
        if (!$id_factura) {
            header('Location: ?route=facturas');
            exit;
        }

        $factura = $this->facturaModel->obtenerPorId($id_factura);
        if (!$factura) {
            header('Location: ?route=facturas');
            exit;
        }

        require __DIR__ . '/../views/facturas/pago.php';
    }
    
    public function exportPdf() {
        $id_factura = $_GET['id_factura'] ?? null;
        if (!$id_factura) {
            header('Location: ?route=facturas&error=ID de factura no válido');
            exit;
        }

        // Obtener los datos de la factura
        $factura = $this->facturaModel->obtenerPorId($id_factura);
        if (!$factura) {
            header('Location: ?route=facturas&error=Factura no encontrada');
            exit;
        }

        // Obtener datos de la empresa desde la tabla config
        $stmtConfig = $this->db->getConnection()->prepare("SELECT nombre_empresa, telefono_empresa, ubicacion_empresa, email_empresa FROM config LIMIT 1");
        $stmtConfig->execute();
        $config = $stmtConfig->get_result()->fetch_assoc();
        
        // Valores por defecto si no hay configuración
        $nombre_empresa = $config['nombre_empresa'] ?? 'Sistema de Gestión Logística';
        $telefono_empresa = $config['telefono_empresa'] ?? '';
        $ubicacion_empresa = $config['ubicacion_empresa'] ?? '';
        $email_empresa = $config['email_empresa'] ?? '';

        // Obtener datos completos del cliente
        $clienteModel = new Cliente($this->db->getConnection());
        $cliente = $clienteModel->obtenerPorId($factura['id_cliente']);

        // Incluir la biblioteca TCPDF
        require_once __DIR__ . '/../vendor/tecnickcom/tcpdf/tcpdf.php';

        // Crear una nueva instancia de TCPDF
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // Configuración del documento
        $pdf->SetCreator($nombre_empresa);
        $pdf->SetAuthor($nombre_empresa);
        $pdf->SetTitle('Factura ' . $factura['numero_factura']);
        $pdf->SetSubject('Factura ' . $factura['numero_factura']);
        $pdf->SetKeywords('Factura, PDF, ' . $factura['numero_factura']);

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
            .header .company-name { font-size: 14pt; font-weight: bold; margin: 10px 0 5px 0; color: #0066cc; }
            .header p { margin: 3px 0; font-size: 9pt; }
            .invoice-info { margin: 20px 0; }
            .two-columns { width: 100%; }
            .two-columns td { vertical-align: top; padding: 10px; }
            .info-box { border: 1px solid #ddd; padding: 10px; background-color: #f9f9f9; }
            .info-box h3 { margin: 0 0 10px 0; font-size: 11pt; color: #333; border-bottom: 1px solid #ddd; padding-bottom: 5px; }
            .info-box p { margin: 5px 0; font-size: 9pt; }
            table.items { width: 100%; border-collapse: collapse; margin: 20px 0; }
            table.items th { background-color: #0066cc; color: white; text-align: left; padding: 10px; border: 1px solid #0066cc; font-size: 10pt; }
            table.items td { padding: 10px; border: 1px solid #ddd; font-size: 9pt; }
            .text-right { text-align: right; }
            .totals { margin-top: 20px; }
            .totals table { width: 50%; margin-left: auto; }
            .totals td { padding: 5px 10px; }
            .total-row { font-weight: bold; font-size: 12pt; background-color: #f0f0f0; }
            .footer { margin-top: 30px; font-size: 8pt; text-align: center; color: #666; border-top: 1px solid #ddd; padding-top: 10px; }
        </style>
        
        <!-- Información de la factura -->
        <div class="invoice-info">
            <h1 style="text-align: center; font-size: 18pt; margin: 20px 0;">FACTURA #' . htmlspecialchars($factura['numero_factura']) . '</h1>
            <p style="text-align: center; margin: 5px 0;"><strong>Fecha de emisión:</strong> ' . date('d/m/Y', strtotime($factura['fecha_emision'])) . '</p>
            <p style="text-align: center; margin: 5px 0;"><strong>Vencimiento:</strong> ' . ($factura['fecha_vencimiento'] ? date('d/m/Y', strtotime($factura['fecha_vencimiento'])) : 'No especificado') . '</p>
        </div>
        
        <!-- Información del cliente y envío -->
        <table class="two-columns">
            <tr>
                <td style="width: 50%;">
                    <div class="info-box">
                        <h3>Datos del Cliente</h3>
                        <p><strong>Nombre:</strong> ' . htmlspecialchars($cliente['cliente'] ?? $factura['cliente']) . '</p>
                        ' . (isset($cliente['telefono']) && $cliente['telefono'] ? '<p><strong>Teléfono:</strong> ' . htmlspecialchars($cliente['telefono']) . '</p>' : '') . '
                        ' . (isset($cliente['email']) && $cliente['email'] ? '<p><strong>Email:</strong> ' . htmlspecialchars($cliente['email']) . '</p>' : '') . '
                    </div>
                </td>
                <td style="width: 50%;">
                    <div class="info-box">
                        <h3>Información del Envío</h3>
                        <p><strong>N° de Seguimiento:</strong> ' . htmlspecialchars($factura['numero_seguimiento'] ?? 'N/A') . '</p>
                    </div>
                </td>
            </tr>
        </table>
        
        <!-- Detalles de la factura -->
        <table class="items">
            <thead>
                <tr>
                    <th>Descripción</th>
                    <th class="text-right">Cantidad</th>
                    <th class="text-right">Precio Unitario</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Servicio de envío #' . htmlspecialchars($factura['numero_seguimiento'] ?? 'N/A') . '</td>
                    <td class="text-right">1</td>
                    <td class="text-right">$' . number_format($factura['subtotal'], 2) . '</td>
                    <td class="text-right">$' . number_format($factura['subtotal'], 2) . '</td>
                </tr>
            </tbody>
        </table>
        
        <!-- Totales -->
        <div class="totals">
            <table>
                <tr>
                    <td><strong>Subtotal:</strong></td>
                    <td class="text-right">$' . number_format($factura['subtotal'], 2) . '</td>
                </tr>
                <tr>
                    <td><strong>IVA (' . $factura['iva'] . '%):</strong></td>
                    <td class="text-right">$' . number_format(($factura['total'] - $factura['subtotal']), 2) . '</td>
                </tr>
                <tr class="total-row">
                    <td><strong>Total:</strong></td>
                    <td class="text-right"><strong>$' . number_format($factura['total'], 2) . '</strong></td>
                </tr>
            </table>
        </div>
        
        <!-- Pie de página -->
        <div class="footer">
              <div class="company-name">' . htmlspecialchars($nombre_empresa) . '</div>
            ' . ($ubicacion_empresa ? '<p><strong>Dirección:</strong> ' . htmlspecialchars($ubicacion_empresa) . '</p>' : '') . '
            ' . ($telefono_empresa ? '<p><strong>Teléfono:</strong> ' . htmlspecialchars($telefono_empresa) . '</p>' : '') . '
            ' . ($email_empresa ? '<p><strong>Email:</strong> ' . htmlspecialchars($email_empresa) . '</p>' : '') . '
        </div>';

        // Escribir el contenido HTML
        $pdf->writeHTML($html, true, false, true, false, '');

        // Cerrar y generar el PDF
        $pdf->Output('factura_' . $factura['numero_factura'] . '.pdf', 'I');
        exit;
    }
}

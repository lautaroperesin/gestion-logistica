<?php
require_once __DIR__ . '/../models/Cliente.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ClienteController {
    private $cliente;
    private $db;

    public function __construct() {
        $this->db = new Database();
        $this->cliente = new Cliente($this->db->getConnection());
    }

    public function index() {
        $buscar = $_GET['buscar'] ?? '';
        $pagina = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $porPagina = 10; // Número de clientes por página
        
        // Obtener el total de clientes para la paginación
        $totalClientes = $this->cliente->contarTotal($buscar);
        $totalPaginas = ceil($totalClientes / $porPagina);
        
        // Asegurarse de que la página actual esté dentro del rango válido
        $pagina = max(1, min($pagina, $totalPaginas));
        
        // Obtener los clientes para la página actual
        $clientes = $this->cliente->obtenerTodos($porPagina, $pagina, $buscar);
        
        include __DIR__ . '/../views/layouts/header.php';
        include __DIR__ . '/../views/clientes/index.php';
        include __DIR__ . '/../views/layouts/footer.php';
    }

    public function create() {
        include __DIR__ . '/../views/layouts/header.php';
        include __DIR__ . '/../views/clientes/form.php';
        include __DIR__ . '/../views/layouts/footer.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = $_POST['cliente'] ?? '';
            $email = $_POST['email'] ?? '';
            $telefono = $_POST['telefono'] ?? '';

            if (!empty($nombre) && !empty($email)) {
                if ($this->cliente->crear($nombre, $email, $telefono)) {
                    header('Location: ?route=clientes&success=Cliente creado exitosamente');
                } else {
                    header('Location: ?route=clientes_create&error=Error al crear el cliente');
                }
            } else {
                header('Location: ?route=clientes_create&error=Todos los campos son requeridos');
            }
        }
        exit();
    }

    public function edit() {
        $id = $_GET['id_cliente'] ?? null;
        if ($id) {
            $cliente = $this->cliente->obtenerPorId($id);
            if ($cliente) {
                include __DIR__ . '/../views/layouts/header.php';
                include __DIR__ . '/../views/clientes/form.php';
                include __DIR__ . '/../views/layouts/footer.php';
            } else {
                header('Location: ?route=clientes&error=Cliente no encontrado');
                exit();
            }
        } else {
            header('Location: ?route=clientes&error=ID de cliente no válido');
            exit();
        }
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id_cliente'] ?? null;
            $nombre = $_POST['cliente'] ?? '';
            $email = $_POST['email'] ?? '';
            $telefono = $_POST['telefono'] ?? '';

            if ($id && !empty($nombre) && !empty($email)) {
                if ($this->cliente->editar($id, $nombre, $email, $telefono)) {
                    header('Location: ?route=clientes&success=Cliente actualizado exitosamente');
                } else {
                    header('Location: ?route=clientes_edit&id_cliente=' . $id . '&error=Error al actualizar el cliente');
                }
            } else {
                header('Location: ?route=clientes_edit&id_cliente=' . $id . '&error=Todos los campos son requeridos');
            }
        }
        exit();
    }

    public function delete() {
        $id = $_GET['id_cliente'] ?? null;
        if ($id) {
            if ($this->cliente->eliminar($id)) {
                header('Location: ?route=clientes&success=Cliente eliminado exitosamente');
            } else {
                header('Location: ?route=clientes&error=Error al eliminar el cliente');
            }
        } else {
            header('Location: ?route=clientes&error=ID de cliente no válido');
        }
        exit();
    }

    public function exportarExcel() {
        $buscar = $_GET['buscar'] ?? '';
        $clientes = $this->cliente->obtenerTodos(999999, 1, $buscar);
        
        // Obtener nombre de la empresa desde la configuración
        $config_query = "SELECT nombre_empresa FROM config LIMIT 1";
        $config_result = $this->db->getConnection()->query($config_query);
        $config = $config_result->fetch_assoc();
        $nombre_empresa = $config['nombre_empresa'] ?? 'Sistema de Gestión Logística';
        
        // Crear nuevo documento de Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Configurar propiedades del documento
        $spreadsheet->getProperties()
            ->setCreator($nombre_empresa)
            ->setTitle("Listado de Clientes")
            ->setSubject("Exportación de Clientes")
            ->setDescription("Listado completo de clientes del sistema");
        
        // Título del documento
        $sheet->setCellValue('A1', 'LISTADO DE CLIENTES');
        $sheet->mergeCells('A1:D1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Fecha de exportación
        $sheet->setCellValue('A2', 'Fecha de exportación: ' . date('d/m/Y H:i:s'));
        $sheet->mergeCells('A2:D2');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Encabezados de columnas
        $sheet->setCellValue('A4', 'ID');
        $sheet->setCellValue('B4', 'Cliente');
        $sheet->setCellValue('C4', 'Email');
        $sheet->setCellValue('D4', 'Teléfono');
        
        // Estilo de encabezados
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 12
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4']
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ];
        $sheet->getStyle('A4:D4')->applyFromArray($headerStyle);
        
        // Agregar datos
        $row = 5;
        if ($clientes && $clientes->num_rows > 0) {
            while ($cliente = $clientes->fetch_assoc()) {
                $sheet->setCellValue('A' . $row, $cliente['id_cliente']);
                $sheet->setCellValue('B' . $row, $cliente['cliente']);
                $sheet->setCellValue('C' . $row, $cliente['email']);
                $sheet->setCellValue('D' . $row, $cliente['telefono']);
                
                // Estilo de filas de datos
                $dataStyle = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'CCCCCC']
                        ]
                    ]
                ];
                $sheet->getStyle('A' . $row . ':D' . $row)->applyFromArray($dataStyle);
                
                // Alternar color de filas
                if ($row % 2 == 0) {
                    $sheet->getStyle('A' . $row . ':D' . $row)->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setRGB('F2F2F2');
                }
                
                $row++;
            }
        }
        
        // Ajustar ancho de columnas
        $sheet->getColumnDimension('A')->setWidth(10);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(35);
        $sheet->getColumnDimension('D')->setWidth(20);
        
        // Configurar encabezados HTTP para descarga
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="clientes_' . date('Y-m-d_His') . '.xlsx"');
        header('Cache-Control: max-age=0');
        
        // Crear el archivo y enviarlo al navegador
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit();
    }
}
?>
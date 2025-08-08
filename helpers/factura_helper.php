<?php
/**
 * Helper para obtener el texto y color del estado de una factura
 */
function getEstadoFactura($estado, $total = null, $total_pagado = null) {
    // Si se proporcionan total y total_pagado, calcular el estado
    if ($total !== null && $total_pagado !== null) {
        $total = floatval($total);
        $total_pagado = floatval($total_pagado);
        
        if ($total_pagado == 0) {
            return ['texto' => 'Emitida', 'color' => 'primary'];
        } elseif ($total_pagado > 0 && $total_pagado < $total) {
            return ['texto' => 'Parcialmente Pagada', 'color' => 'warning'];
        } elseif ($total_pagado >= $total) {
            return ['texto' => 'Pagada', 'color' => 'success'];
        }
    }
    
    // Si no se proporcionan valores, usar el estado base
    $estados = [
        1 => ['texto' => 'Emitida', 'color' => 'primary'],
        2 => ['texto' => 'Parcialmente Pagada', 'color' => 'warning'],
        3 => ['texto' => 'Pagada', 'color' => 'success'],
        4 => ['texto' => 'Vencida', 'color' => 'danger'],
        5 => ['texto' => 'Anulada', 'color' => 'secondary']
    ];
    
    return $estados[$estado] ?? ['texto' => 'Desconocido', 'color' => 'info'];
}

/**
 * Helper para obtener el badge HTML del estado
 */
function getBadgeEstadoFactura($estado, $total = null, $total_pagado = null) {
    $estadoInfo = getEstadoFactura($estado, $total, $total_pagado);
    return sprintf(
        '<span class="badge bg-%s">%s</span>',
        $estadoInfo['color'],
        $estadoInfo['texto']
    );
}

/**
 * Helper para calcular el estado basado en montos
 */
function calcularEstadoFactura($total, $total_pagado, $fecha_vencimiento = null) {
    $total = floatval($total);
    $total_pagado = floatval($total_pagado);
    
    // Primero, verificar si está vencida
    if ($fecha_vencimiento) {
        $fecha_vencimiento = strtotime($fecha_vencimiento);
        $fecha_actual = strtotime(date('Y-m-d'));
        if ($fecha_actual > $fecha_vencimiento && $total_pagado < $total) {
            return 4; // Vencida
        }
    }
    
    // Luego, verificar el estado basado en los montos
    if ($total_pagado == 0) {
        return 1; // Emitida
    } elseif ($total_pagado > 0 && $total_pagado < $total) {
        return 2; // Parcialmente Pagada
    } elseif ($total_pagado >= $total) {
        return 3; // Pagada
    }
    
    return 1; // Por defecto Emitida
}
?>

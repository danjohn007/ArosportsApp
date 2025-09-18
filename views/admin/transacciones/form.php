<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="bi bi-receipt"></i> Nueva Transacción
        </h1>
        <a href="<?= BASE_URL ?>/admin/transacciones" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>

    <?php if (isset($error)): ?>
    <div class="alert alert-danger">
        <i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
    </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Información de la Transacción</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="<?= BASE_URL ?>/admin/transacciones?action=create">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tipo" class="form-label">Tipo de Transacción <span class="text-danger">*</span></label>
                                    <select name="tipo" id="tipo" class="form-select" required onchange="filtrarCategorias()">
                                        <option value="">Seleccione un tipo</option>
                                        <option value="ingreso" <?= ($old['tipo'] ?? '') === 'ingreso' ? 'selected' : '' ?>>Ingreso</option>
                                        <option value="gasto" <?= ($old['tipo'] ?? 'gasto') === 'gasto' ? 'selected' : '' ?>>Gasto</option>
                                        <option value="retiro" <?= ($old['tipo'] ?? '') === 'retiro' ? 'selected' : '' ?>>Retiro</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="categoria_id" class="form-label">Categoría <span class="text-danger">*</span></label>
                                    <select name="categoria_id" id="categoria_id" class="form-select" required>
                                        <option value="">Seleccione una categoría</option>
                                        <?php foreach ($categorias as $categoria): ?>
                                        <option value="<?= $categoria['id'] ?>" 
                                                data-tipo="<?= $categoria['tipo'] ?>"
                                                <?= ($old['categoria_id'] ?? '') == $categoria['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($categoria['nombre']) ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="concepto" class="form-label">Concepto <span class="text-danger">*</span></label>
                                    <input type="text" name="concepto" id="concepto" class="form-control" 
                                           value="<?= htmlspecialchars($old['concepto'] ?? '') ?>" 
                                           placeholder="Breve descripción del concepto" required maxlength="200">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="monto" class="form-label">Monto <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" name="monto" id="monto" class="form-control" 
                                               value="<?= htmlspecialchars($old['monto'] ?? '') ?>" 
                                               step="0.01" min="0.01" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción Detallada</label>
                            <textarea name="descripcion" id="descripcion" class="form-control" rows="3" 
                                      placeholder="Descripción detallada de la transacción (opcional)"><?= htmlspecialchars($old['descripcion'] ?? '') ?></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="fecha_transaccion" class="form-label">Fecha de Transacción <span class="text-danger">*</span></label>
                                    <input type="date" name="fecha_transaccion" id="fecha_transaccion" class="form-control" 
                                           value="<?= htmlspecialchars($old['fecha_transaccion'] ?? date('Y-m-d')) ?>" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="metodo_pago" class="form-label">Método de Pago</label>
                                    <select name="metodo_pago" id="metodo_pago" class="form-select">
                                        <option value="efectivo" <?= ($old['metodo_pago'] ?? 'efectivo') === 'efectivo' ? 'selected' : '' ?>>Efectivo</option>
                                        <option value="transferencia" <?= ($old['metodo_pago'] ?? '') === 'transferencia' ? 'selected' : '' ?>>Transferencia</option>
                                        <option value="cheque" <?= ($old['metodo_pago'] ?? '') === 'cheque' ? 'selected' : '' ?>>Cheque</option>
                                        <option value="tarjeta" <?= ($old['metodo_pago'] ?? '') === 'tarjeta' ? 'selected' : '' ?>>Tarjeta</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="club_id" class="form-label">Club Asociado</label>
                                    <select name="club_id" id="club_id" class="form-select">
                                        <option value="">Sin club específico</option>
                                        <?php foreach ($clubes as $club): ?>
                                        <option value="<?= $club['id'] ?>" <?= ($old['club_id'] ?? '') == $club['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($club['nombre']) ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="referencia" class="form-label">Referencia/Folio</label>
                            <input type="text" name="referencia" id="referencia" class="form-control" 
                                   value="<?= htmlspecialchars($old['referencia'] ?? '') ?>" 
                                   placeholder="Número de cheque, referencia de transferencia, etc." maxlength="100">
                        </div>

                        <div class="mb-3">
                            <label for="observaciones" class="form-label">Observaciones</label>
                            <textarea name="observaciones" id="observaciones" class="form-control" rows="2" 
                                      placeholder="Observaciones adicionales (opcional)"><?= htmlspecialchars($old['observaciones'] ?? '') ?></textarea>
                        </div>

                        <hr>
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="form-text">
                                <i class="bi bi-info-circle"></i> 
                                <?php if ($_SESSION['user']['tipo_usuario'] === 'superadmin'): ?>
                                    Como superadmin, la transacción será <strong>autorizada automáticamente</strong>.
                                <?php else: ?>
                                    La transacción quedará <strong>pendiente de autorización</strong> por un administrador.
                                <?php endif; ?>
                            </div>
                            
                            <div>
                                <button type="button" class="btn btn-secondary me-2" onclick="history.back()">
                                    <i class="bi bi-x-circle"></i> Cancelar
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle"></i> Guardar Transacción
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <!-- Panel de ayuda -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">
                        <i class="bi bi-question-circle"></i> Ayuda
                    </h6>
                </div>
                <div class="card-body">
                    <h6 class="text-primary">Tipos de Transacción:</h6>
                    <ul class="list-unstyled small">
                        <li><span class="badge bg-success">Ingreso</span> - Dinero que entra al sistema</li>
                        <li><span class="badge bg-danger">Gasto</span> - Dinero que sale para operaciones</li>
                        <li><span class="badge bg-warning">Retiro</span> - Dinero que se retira del sistema</li>
                    </ul>
                    
                    <h6 class="text-primary mt-3">Métodos de Pago:</h6>
                    <ul class="list-unstyled small">
                        <li><strong>Efectivo:</strong> Pago en dinero físico</li>
                        <li><strong>Transferencia:</strong> Transferencia bancaria</li>
                        <li><strong>Cheque:</strong> Pago con cheque</li>
                        <li><strong>Tarjeta:</strong> Pago con tarjeta de crédito/débito</li>
                    </ul>
                    
                    <div class="alert alert-info small mt-3">
                        <i class="bi bi-lightbulb"></i> 
                        <strong>Tip:</strong> Use el campo "Referencia" para guardar números de cheque, 
                        referencias de transferencia o cualquier identificador importante.
                    </div>
                </div>
            </div>
            
            <!-- Panel de categorías -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">
                        <i class="bi bi-tags"></i> Categorías Disponibles
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <h6 class="text-success small">INGRESOS</h6>
                            <?php foreach ($categorias as $categoria): ?>
                                <?php if ($categoria['tipo'] === 'ingreso'): ?>
                                <span class="badge mb-1" style="background-color: <?= $categoria['color'] ?>;">
                                    <?= htmlspecialchars($categoria['nombre']) ?>
                                </span>
                                <?php endif; ?>
                            <?php endforeach; ?>
                            
                            <h6 class="text-danger small mt-3">GASTOS</h6>
                            <?php foreach ($categorias as $categoria): ?>
                                <?php if ($categoria['tipo'] === 'gasto'): ?>
                                <span class="badge mb-1" style="background-color: <?= $categoria['color'] ?>;">
                                    <?= htmlspecialchars($categoria['nombre']) ?>
                                </span>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function filtrarCategorias() {
    const tipoSelect = document.getElementById('tipo');
    const categoriaSelect = document.getElementById('categoria_id');
    const selectedTipo = tipoSelect.value;
    
    // Mostrar todas las opciones primero
    Array.from(categoriaSelect.options).forEach(option => {
        if (option.value === '') {
            option.style.display = 'block';
            return;
        }
        
        const categoriaType = option.getAttribute('data-tipo');
        
        if (selectedTipo === '') {
            option.style.display = 'block';
        } else if (selectedTipo === 'retiro') {
            // Para retiro, mostrar categorías de gasto
            option.style.display = categoriaType === 'gasto' ? 'block' : 'none';
        } else {
            // Para ingreso y gasto, mostrar categorías del mismo tipo
            option.style.display = categoriaType === selectedTipo ? 'block' : 'none';
        }
    });
    
    // Limpiar selección si no es válida
    if (categoriaSelect.value) {
        const selectedOption = categoriaSelect.querySelector(`option[value="${categoriaSelect.value}"]`);
        if (selectedOption && selectedOption.style.display === 'none') {
            categoriaSelect.value = '';
        }
    }
}

// Filtrar categorías al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    filtrarCategorias();
});
</script>
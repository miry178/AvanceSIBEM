<?php
require_once '../../bd/conexion.php';

$areas       = $conn->query("SELECT * FROM Area");
$carreras    = $conn->query("SELECT * FROM Carrera");
$tipos       = $conn->query("SELECT * FROM TipoMaterial");
$editoriales = $conn->query("SELECT * FROM Editorial ORDER BY nombre");

$areasArr       = $areas->fetch_all(MYSQLI_ASSOC);
$carrerasArr    = $carreras->fetch_all(MYSQLI_ASSOC);
$tiposArr       = $tipos->fetch_all(MYSQLI_ASSOC);
$editorialesArr = $editoriales->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIBEM - Agregar Material</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="add-libro-diseno.css">
</head>
<body>


<div id="formAgregar" class="bg-white rounded p-4 shadow-sm mb-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-semibold mb-0">Agregar Material</h5>
        <button type="button" class="btn-close" onclick="cerrarFormulario()"></button>
    </div>

    <form method="POST" action="procesar_agregar_material.php">

        <!-- Tipo de material — siempre visible, controla los demás campos -->
        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Tipo de material <span class="text-danger">*</span></label>
                <select name="idTipoMaterial" id="tipoMaterial" class="form-select" required onchange="actualizarCampos()">
                    <option value="">Seleccione el tipo</option>
                    <?php foreach ($tiposArr as $t): ?>
                        <option value="<?= $t['idTipoMaterial'] ?>" data-tipo="<?= strtolower($t['descripcion']) ?>">
                            <?= $t['descripcion'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Título <span class="text-danger">*</span></label>
                <input type="text" name="titulo" class="form-control" placeholder="Título del material" required>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Autor <span class="text-danger">*</span></label>
                <input type="text" name="autor" class="form-control" placeholder="Autor" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Año de publicación <span class="text-danger">*</span></label>
                <input type="number" name="anioPublicacion" class="form-control" placeholder="Ej: 2023" min="1900" max="2099" required>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Editorial</label>
                <select name="idEditorial" class="form-select">
                    <option value="">Seleccione la editorial</option>
                    <?php foreach ($editorialesArr as $e): ?>
                        <option value="<?= $e['idEditorial'] ?>"><?= htmlspecialchars($e['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Edición</label>
                <input type="text" name="edicion" class="form-control" placeholder="Ej: 1ra">
            </div>
        </div>

        <!-- ISBN — solo Libro -->
        <div class="row g-3 mb-3" id="campoISBN" style="display:none">
            <div class="col-md-6">
                <label class="form-label fw-semibold">ISBN</label>
                <input type="text" name="isbn" id="inputISBN" class="form-control" placeholder="ISBN del libro">
            </div>
        </div>

        <!-- Área — Libro y Revista -->
        <div class="row g-3 mb-3" id="campoArea" style="display:none">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Área <span class="text-danger">*</span></label>
                <select name="idArea" id="selectArea" class="form-select">
                    <option value="">Seleccione el área</option>
                    <?php foreach ($areasArr as $a): ?>
                        <option value="<?= $a['idArea'] ?>"><?= $a['descripcion'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- Carrera — Tesis y Residencia -->
        <div class="row g-3 mb-3" id="campoCarrera" style="display:none">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Carrera <span class="text-danger">*</span></label>
                <select name="idCarrera" id="selectCarrera" class="form-select">
                    <option value="">Seleccione la carrera</option>
                    <?php foreach ($carrerasArr as $c): ?>
                        <option value="<?= $c['idCarrera'] ?>"><?= $c['descripcion'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- Es prestable — solo Libro y Revista -->
        <div class="row g-3 mb-3" id="campoPrestable" style="display:none">
 
            <div class="col-md-6">
                <label class="form-label fw-semibold">Número de ejemplares <span class="text-danger">*</span></label>
                <input type="number" name="ejemplares" id="inputEjemplares" class="form-control" placeholder="Ej: 3" min="1">
            </div>
        </div>

        <!-- Ejemplares para tipos NO prestables -->
        <div class="row g-3 mb-3" id="campoEjemplaresSolo" style="display:none">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Número de ejemplares <span class="text-danger">*</span></label>
                <input type="number" name="ejemplares_np" class="form-control" placeholder="Ej: 1" min="1">
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-2">
            <button type="button" class="btn btn-success px-4" onclick="validarFormulario()">Agregar Material</button>
            <button type="button" class="btn btn-danger px-4" onclick="cerrarFormulario()">Cancelar</button>
        </div>
    </form>
</div>
</body>
</html>
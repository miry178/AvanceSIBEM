<?php
header('Content-Type: application/json');
require_once '../../bd/conexion.php';

// ── DESACTIVAR usuario ─────────────────────────────────────────
if (!empty($_POST['eliminar'])) {
    $id = trim($_POST['idUsuario'] ?? '');
    if (!$id) { echo json_encode(['ok' => false, 'error' => 'ID inválido']); exit; }

    $stmt = $conn->prepare("UPDATE Usuario SET activo = 'no' WHERE idUsuario = ?");
    $stmt->bind_param("s", $id);
    if ($stmt->execute()) {
        echo json_encode(['ok' => true, 'mensaje' => 'Usuario marcado como inactivo correctamente']);
    } else {
        echo json_encode(['ok' => false, 'error' => $conn->error]);
    }
    exit;
}

// ── TOGGLE activar/desactivar ──────────────────────────────────
if (!empty($_POST['toggle'])) {
    $id     = trim($_POST['idUsuario'] ?? '');
    $activo = ($_POST['activo'] ?? '') === 'si' ? 'si' : 'no';
    if (!$id) { echo json_encode(['ok' => false, 'error' => 'ID inválido']); exit; }

    $stmt = $conn->prepare("UPDATE Usuario SET activo = ? WHERE idUsuario = ?");
    $stmt->bind_param("ss", $activo, $id);
    if ($stmt->execute()) {
        $mensaje = $activo === 'si' ? 'Usuario activado correctamente' : 'Usuario desactivado correctamente';
        echo json_encode(['ok' => true, 'mensaje' => $mensaje]);
    } else {
        echo json_encode(['ok' => false, 'error' => $conn->error]);
    }
    exit;
}

// ── GUARDAR (nuevo o editar) ───────────────────────────────────
$idUsuario     = trim($_POST['idUsuario']     ?? '');
$nombre        = trim($_POST['nombre']        ?? '');
$correoInst    = trim($_POST['correoInst']    ?? '');
$idTipoPersona = intval($_POST['idTipoPersona'] ?? 0);
$activo        = ($_POST['activo'] ?? '') === 'si' ? 'si' : 'no';
$idCarrera     = intval($_POST['idCarrera']   ?? 0);
$idDivision    = intval($_POST['idDivision']  ?? 0);
$editando      = trim($_POST['editando']      ?? '');

if (!$idUsuario || !$nombre || !$correoInst || !$idTipoPersona) {
    echo json_encode(['ok' => false, 'error' => 'Faltan campos obligatorios']); exit;
}

$conn->begin_transaction();

try {
    if ($editando) {
        // Verificar que no haya más de un Administrador al editar
        if ($idTipoPersona == 1) {
            $checkAdmin = $conn->prepare("SELECT COUNT(*) AS total FROM Usuario WHERE idRol = 1 AND idUsuario != ?");
            $checkAdmin->bind_param("s", $editando);
            $checkAdmin->execute();
            $totalAdmins = $checkAdmin->get_result()->fetch_assoc()['total'];
            if ($totalAdmins >= 1) {
                echo json_encode(['ok' => false, 'error' => 'Ya existe un Administrador en el sistema. Solo puede haber uno.']);
                $conn->rollback(); exit;
            }
        }

        // Editar sin tocar la contraseña
        $stmt = $conn->prepare("UPDATE Usuario SET nombre=?, correoInst=?, activo=?, idRol=? WHERE idUsuario=?");
        $stmt->bind_param("sssii", $nombre, $correoInst, $activo, $idTipoPersona, $idUsuario);
        $stmt->execute();

        // Actualizar rol en RelRol
        $stmt2 = $conn->prepare("UPDATE RelRol SET idRol=? WHERE idUsuario=?");
        $stmt2->bind_param("is", $idTipoPersona, $idUsuario);
        $stmt2->execute();

        // Actualizar carrera (Alumno = 5)
        if ($idTipoPersona == 5 && $idCarrera) {
            $existe = $conn->prepare("SELECT idUsuario FROM Alumno WHERE idUsuario=?");
            $existe->bind_param("s", $idUsuario);
            $existe->execute();
            if ($existe->get_result()->fetch_assoc()) {
                $s = $conn->prepare("UPDATE Alumno SET idCarrera=? WHERE idUsuario=?");
                $s->bind_param("is", $idCarrera, $idUsuario);
                $s->execute();
            } else {
                $s = $conn->prepare("INSERT INTO Alumno (idUsuario, idCarrera) VALUES (?,?)");
                $s->bind_param("si", $idUsuario, $idCarrera);
                $s->execute();
            }
        }

        // Actualizar división (Docente = 4)
        if ($idTipoPersona == 4 && $idDivision) {
            $existe = $conn->prepare("SELECT idUsuario FROM Docente WHERE idUsuario=?");
            $existe->bind_param("s", $idUsuario);
            $existe->execute();
            if ($existe->get_result()->fetch_assoc()) {
                $s = $conn->prepare("UPDATE Docente SET idDivision=? WHERE idUsuario=?");
                $s->bind_param("is", $idDivision, $idUsuario);
                $s->execute();
            } else {
                $s = $conn->prepare("INSERT INTO Docente (idUsuario, idDivision) VALUES (?,?)");
                $s->bind_param("si", $idUsuario, $idDivision);
                $s->execute();
            }
        }

        $conn->commit();
        echo json_encode(['ok' => true, 'mensaje' => 'Usuario actualizado correctamente']);

    } else {
        // Verificar que no exista el mismo ID
        $check = $conn->prepare("SELECT idUsuario FROM Usuario WHERE idUsuario=?");
        $check->bind_param("s", $idUsuario);
        $check->execute();
        if ($check->get_result()->fetch_assoc()) {
            echo json_encode(['ok' => false, 'error' => 'Ya existe un usuario con ese No. Control / RFC']);
            $conn->rollback(); exit;
        }

        // Verificar que no exista el mismo correo
        $checkCorreo = $conn->prepare("SELECT idUsuario FROM Usuario WHERE correoInst = ? AND idUsuario != ?");
        $checkCorreo->bind_param("ss", $correoInst, $idUsuario);
        $checkCorreo->execute();
        if ($checkCorreo->get_result()->fetch_assoc()) {
            echo json_encode(['ok' => false, 'error' => 'Ya existe un usuario con ese correo institucional']);
            $conn->rollback(); exit;
        }

        // Verificar que no haya más de un Administrador al crear
        if ($idTipoPersona == 1) {
            $checkAdmin = $conn->prepare("SELECT COUNT(*) AS total FROM Usuario WHERE idRol = 1");
            $checkAdmin->execute();
            $totalAdmins = $checkAdmin->get_result()->fetch_assoc()['total'];
            if ($totalAdmins >= 1) {
                echo json_encode(['ok' => false, 'error' => 'Ya existe un Administrador en el sistema. Solo puede haber uno.']);
                $conn->rollback(); exit;
            }
        }

        // Insertar en Usuario
        $stmt = $conn->prepare("INSERT INTO Usuario (idUsuario, correoInst, nombre, activo, password, idRol) VALUES (?,?,?,?,?,?)");
        $pass = null;
        $stmt->bind_param("sssssi", $idUsuario, $correoInst, $nombre, $activo, $pass, $idTipoPersona);
        $stmt->execute();

        // Insertar en RelRol
        $stmt2 = $conn->prepare("INSERT INTO RelRol (idUsuario, correoInst, idRol) VALUES (?,?,?)");
        $stmt2->bind_param("ssi", $idUsuario, $correoInst, $idTipoPersona);
        $stmt2->execute();

        // Insertar en Alumno si es Alumno (idRol=5)
        if ($idTipoPersona == 5 && $idCarrera) {
            $s = $conn->prepare("INSERT INTO Alumno (idUsuario, idCarrera) VALUES (?,?)");
            $s->bind_param("si", $idUsuario, $idCarrera);
            $s->execute();
        }

        // Insertar en Docente si es Docente (idRol=4)
        if ($idTipoPersona == 4 && $idDivision) {
            $s = $conn->prepare("INSERT INTO Docente (idUsuario, idDivision) VALUES (?,?)");
            $s->bind_param("si", $idUsuario, $idDivision);
            $s->execute();
        }

        $conn->commit();
        echo json_encode(['ok' => true, 'mensaje' => 'Usuario registrado correctamente']);
    }

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
}
?>
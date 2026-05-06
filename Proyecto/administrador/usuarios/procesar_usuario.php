<?php
header('Content-Type: application/json');

$conn = new mysqli("localhost", "root", "5775", "biblioteca");
if ($conn->connect_error) {
    echo json_encode(['ok' => false, 'error' => 'Error de conexión']);
    exit;
}

$pdo = new PDO("mysql:host=localhost;dbname=biblioteca;charset=utf8mb4", "root", "5775", [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
]);

// ── DESACTIVAR usuario ─────────────────────────────────────────
if (!empty($_POST['eliminar'])) {
    $id = trim($_POST['idUsuario'] ?? '');
    if (!$id) { echo json_encode(['ok' => false, 'error' => 'ID inválido']); exit; }

    try {
        $pdo->prepare("UPDATE Usuario SET activo = 'no' WHERE idUsuario = ?")
            ->execute([$id]);
        echo json_encode(['ok' => true, 'mensaje' => 'Usuario marcado como inactivo correctamente']);
    } catch (Exception $e) {
        echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
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

try {
    $pdo->beginTransaction();

    if ($editando) {
        // Editar sin tocar la contraseña
        $pdo->prepare("UPDATE Usuario SET nombre=?, correoInst=?, activo=?, idRol=? WHERE idUsuario=?")
            ->execute([$nombre, $correoInst, $activo, $idTipoPersona, $idUsuario]);

        // Actualizar rol en RelRol
        $pdo->prepare("UPDATE RelRol SET idRol=? WHERE idUsuario=?")
            ->execute([$idTipoPersona, $idUsuario]);

        // Actualizar carrera (Alumno = 5) o división (Docente = 4)
        if ($idTipoPersona == 5 && $idCarrera) {
            $existe = $pdo->prepare("SELECT idUsuario FROM Alumno WHERE idUsuario=?");
            $existe->execute([$idUsuario]);
            if ($existe->fetch()) {
                $pdo->prepare("UPDATE Alumno SET idCarrera=? WHERE idUsuario=?")->execute([$idCarrera, $idUsuario]);
            } else {
                $pdo->prepare("INSERT INTO Alumno (idUsuario, idCarrera) VALUES (?,?)")->execute([$idUsuario, $idCarrera]);
            }
        }
        if ($idTipoPersona == 4 && $idDivision) {
            $existe = $pdo->prepare("SELECT idUsuario FROM Docente WHERE idUsuario=?");
            $existe->execute([$idUsuario]);
            if ($existe->fetch()) {
                $pdo->prepare("UPDATE Docente SET idDivision=? WHERE idUsuario=?")->execute([$idDivision, $idUsuario]);
            } else {
                $pdo->prepare("INSERT INTO Docente (idUsuario, idDivision) VALUES (?,?)")->execute([$idUsuario, $idDivision]);
            }
        }

        $pdo->commit();
        echo json_encode(['ok' => true, 'mensaje' => 'Usuario actualizado correctamente']);

    } else {
        // Verificar que no exista
        $check = $pdo->prepare("SELECT idUsuario FROM Usuario WHERE idUsuario=?");
        $check->execute([$idUsuario]);
        if ($check->fetch()) {
            echo json_encode(['ok' => false, 'error' => 'Ya existe un usuario con ese No. Control / RFC']);
            $pdo->rollBack(); exit;
        }

        // Insertar en Usuario con contraseña NULL e idRol
        $pdo->prepare("INSERT INTO Usuario (idUsuario, correoInst, nombre, activo, password, idRol) VALUES (?,?,?,?,?,?)")
            ->execute([$idUsuario, $correoInst, $nombre, $activo, null, $idTipoPersona]);

        // Insertar en RelRol
        $pdo->prepare("INSERT INTO RelRol (idUsuario, correoInst, idRol) VALUES (?,?,?)")
            ->execute([$idUsuario, $correoInst, $idTipoPersona]);

        // Insertar en Alumno o Docente según rol
        if ($idTipoPersona == 5 && $idCarrera) {
            $pdo->prepare("INSERT INTO Alumno (idUsuario, idCarrera) VALUES (?,?)")->execute([$idUsuario, $idCarrera]);
        }
        if ($idTipoPersona == 4 && $idDivision) {
            $pdo->prepare("INSERT INTO Docente (idUsuario, idDivision) VALUES (?,?)")->execute([$idUsuario, $idDivision]);
        }

        $pdo->commit();
        echo json_encode(['ok' => true, 'mensaje' => 'Usuario registrado correctamente']);
    }

} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
}
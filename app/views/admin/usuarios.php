<?php
$pageTitle = 'Usuarios';
$pageSubtitle = 'Crea cuentas, asigna roles y administra el estado de acceso.';

require __DIR__ . '/../partials/header.php';
?>

<?php if (isset($_GET['ok'])): ?>
    <div class="success">Usuario creado correctamente.</div>
<?php endif; ?>

<?php if (isset($_GET['editado'])): ?>
    <div class="success">Datos del usuario actualizados correctamente.</div>
<?php endif; ?>

<div class="stats">
    <div class="card stat">
        <div class="stat-icon">♙</div>
        <div>
            <span>Total usuarios</span>
            <strong><?= count($usuarios) ?></strong>
        </div>
    </div>

    <div class="card stat">
        <div class="stat-icon">✓</div>
        <div>
            <span>Usuarios activos</span>
            <strong><?= count(array_filter($usuarios, fn($u) => (int) $u['id_estado_u'] === 1)) ?></strong>
        </div>
    </div>

    <div class="card stat">
        <div class="stat-icon">♙</div>
        <div>
            <span>Instructores</span>
            <strong><?= count(array_filter($usuarios, fn($u) => (int) $u['id_rol_u'] === 2)) ?></strong>
        </div>
    </div>

    <div class="card stat">
        <div class="stat-icon">🎓</div>
        <div>
            <span>Aprendices</span>
            <strong><?= count(array_filter($usuarios, fn($u) => (int) $u['id_rol_u'] === 3)) ?></strong>
        </div>
    </div>
</div>

<div class="toolbar">
    <form style="display:flex;gap:10px;flex:1" method="get">
        <input type="hidden" name="route" value="usuarios">

        <input
            class="search"
            name="q"
            value="<?= htmlspecialchars($_GET['q'] ?? '') ?>"
            placeholder="Buscar por nombre, correo o rol..."
        >

        <select class="search" style="max-width:190px" name="rol">
            <option value="">Todos los roles</option>
            <option value="1" <?= ($_GET['rol'] ?? '') === '1' ? 'selected' : '' ?>>Administrador</option>
            <option value="2" <?= ($_GET['rol'] ?? '') === '2' ? 'selected' : '' ?>>Instructor</option>
            <option value="3" <?= ($_GET['rol'] ?? '') === '3' ? 'selected' : '' ?>>Aprendiz</option>
        </select>

        <button class="btn btn-secondary" type="submit">Buscar</button>
    </form>

    <div class="actions">
        <a class="btn btn-secondary" href="index.php?route=asignar">Asignar curso</a>
        <a class="btn btn-primary" href="index.php?route=nuevo-usuario">+ Nuevo usuario</a>
        <a class="btn btn-secondary" href="index.php?route=importar-usuarios">Crear usuarios masivamente</a>
    </div>
</div>

<div class="table-wrap">
    <table class="table">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Correo</th>
                <th>Usuario</th>
                <th>Rol</th>
                <th>Estado</th>
                <th>Acción</th>
            </tr>
        </thead>

        <tbody>
        <?php foreach ($usuarios as $u): ?>
            <tr>
                <td>
                    <strong><?= htmlspecialchars($u['nombre'] . ' ' . $u['apellido']) ?></strong><br>
                    <small><?= htmlspecialchars($u['id_usuario']) ?></small>
                </td>
                <td><?= htmlspecialchars($u['correo']) ?></td>
                <td><?= htmlspecialchars($u['username']) ?></td>
                <td><?= htmlspecialchars($u['nombre_rol']) ?></td>
                <td>
                    <span class="badge <?= (int) $u['id_estado_u'] === 1 ? '' : 'gray' ?>">
                        <?= htmlspecialchars($u['nombre_estado']) ?>
                    </span>
                </td>
                 <td style="display:flex;gap:8px;align-items:center;white-space:nowrap">
                     <form method="post" action="index.php?route=estado-usuario">
                         <input type="hidden" name="id" value="<?= htmlspecialchars($u['id_usuario']) ?>">
                         <input type="hidden" name="estado" value="<?= (int) $u['id_estado_u'] === 1 ? 2 : 1 ?>">
                         <button class="btn btn-secondary" type="submit">
                             <?= (int) $u['id_estado_u'] === 1 ? 'Desactivar' : 'Activar' ?>
                         </button>
                     </form>
                     <a class="btn btn-secondary" href="index.php?route=editar-usuario&id=<?= htmlspecialchars($u['id_usuario']) ?>">Editar</a>
                 </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>

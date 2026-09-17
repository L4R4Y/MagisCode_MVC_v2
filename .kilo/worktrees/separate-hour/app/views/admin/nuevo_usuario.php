<?php
$pageTitle = 'Nuevo usuario';
$pageSubtitle = 'Crea una nueva cuenta para la plataforma.';
require __DIR__ . '/../partials/header.php';
?>
<?php if (!empty($error)): ?><div class="alert"><?= htmlspecialchars($error) ?></div><?php endif; ?>

<div class="panel">
    <p class="form-note">Los campos marcados con <span class="req">*</span> son obligatorios.</p>

    <form method="post" class="form-grid">
        <div class="field">
            <label>Documento <span class="req">*</span></label>
            <input name="id_usuario" required>
        </div>

        <div class="field">
            <label>Tipo de documento <span class="req">*</span></label>
            <select name="tipo_documento" required>
                <option value="1">CC</option>
                <option value="2">CE</option>
                <option value="3">TI</option>
                <option value="4">PASAPORTE</option>
            </select>
        </div>

        <div class="field">
            <label>Nombre <span class="req">*</span></label>
            <input name="nombre" required>
        </div>

        <div class="field">
            <label>Apellido <span class="req">*</span></label>
            <input name="apellido" required>
        </div>

        <div class="field">
            <label>Correo <span class="req">*</span></label>
            <input type="email" name="correo" required>
        </div>

        <div class="field">
            <label>Rol <span class="req">*</span></label>
            <select name="rol" required>
                <option value="3">Aprendiz</option>
                <option value="2">Instructor</option>
                <option value="1">Administrador</option>
            </select>
        </div>

        <div class="field">
            <label>Contraseña temporal <span class="req">*</span></label>
            <input type="password" name="password" value="hash123" required>
        </div>

        <div class="field full">
            <button class="btn btn-primary">Crear usuario</button>
        </div>
    </form>
</div>
<?php require __DIR__ . '/../partials/footer.php'; ?>

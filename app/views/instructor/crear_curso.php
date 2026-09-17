<?php
$pageTitle = 'Nuevo curso';
$pageSubtitle = 'Crea un curso para tus aprendices.';
require __DIR__ . '/../partials/header.php';
?>
<div class="panel">
    <p class="form-note">Los campos marcados con <span class="req">*</span> son obligatorios.</p>

    <form method="post" class="form-grid">
        <div class="field full">
            <label>Título del curso <span class="req">*</span></label>
            <input name="titulo" required>
        </div>
        <div class="field full">
            <label>Descripción <span class="req">*</span></label>
            <textarea name="descripcion" required></textarea>
        </div>
        <div class="field full">
            <label>Ruta de imagen (opcional)</label>
            <input name="imagen" placeholder="img/python_basico.jpg">
        </div>
        <div class="field full">
            <button class="btn btn-primary">Guardar curso</button>
        </div>
    </form>
</div>
<?php require __DIR__ . '/../partials/footer.php'; ?>

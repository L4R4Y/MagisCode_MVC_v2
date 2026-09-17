<?php
$pageTitle = 'Nueva evaluación';
$pageSubtitle = 'Crea una evaluación para el curso seleccionado.';
require __DIR__ . '/../partials/header.php';
?>
<div class="panel">
    <p class="form-note">Los campos marcados con <span class="req">*</span> son obligatorios.</p>

    <form method="post" class="form-grid">
        <div class="field full">
            <label>Título <span class="req">*</span></label>
            <input name="titulo" required>
        </div>
        <div class="field full">
            <label>Descripción <span class="req">*</span></label>
            <textarea name="descripcion" required></textarea>
        </div>
        <div class="field">
            <label>Puntaje mínimo <span class="req">*</span></label>
            <input type="number" step="0.01" min="0" max="5" name="aprobacion" value="3" required>
        </div>
        <div class="field">
            <button class="btn btn-primary">Crear evaluación</button>
        </div>
    </form>
</div>
<?php require __DIR__ . '/../partials/footer.php'; ?>

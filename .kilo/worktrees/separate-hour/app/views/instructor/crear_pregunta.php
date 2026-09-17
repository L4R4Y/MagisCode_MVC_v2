<?php
$pageTitle = 'Agregar pregunta';
$pageSubtitle = 'Construye las preguntas y respuestas de la evaluación.';
require __DIR__ . '/../partials/header.php';
?>
<div class="panel">
    <p class="form-note">Los campos marcados con <span class="req">*</span> son obligatorios.</p>

    <form method="post">
        <div class="field">
            <label>Tipo <span class="req">*</span></label>
            <select name="tipo" required>
                <option>Seleccion Multiple</option>
                <option>Verdadero/Falso</option>
            </select>
        </div>
        <div class="field">
            <label>Pregunta <span class="req">*</span></label>
            <textarea name="pregunta" required></textarea>
        </div>
        <?php for ($i = 0; $i < 4; $i++): ?>
            <div class="field">
                <label>Respuesta <?= $i + 1 ?> <span class="req">*</span></label>
                <input name="respuesta[]" required>
                <label><input type="radio" name="correcta" value="<?= $i ?>" <?= ($i === 0 ? 'checked' : '') ?>> Correcta</label>
            </div>
        <?php endfor; ?>
        <br>
        <button class="btn btn-primary">Guardar pregunta</button>
    </form>
</div>
<?php require __DIR__ . '/../partials/footer.php'; ?>

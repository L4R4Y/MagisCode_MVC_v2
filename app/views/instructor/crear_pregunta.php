<?php
$pageTitle = 'Agregar pregunta';
$pageSubtitle = 'Construye las preguntas y respuestas de la evaluación.';
require __DIR__ . '/../partials/header.php';
?>
<div class="panel">
    <?php if (!empty($error)): ?><div class="alert"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <p class="form-note">Los campos marcados con <span class="req">*</span> son obligatorios.</p>

    <form method="post" id="form-pregunta">
        <div class="field">
            <label>Tipo <span class="req">*</span></label>
            <select name="tipo" id="tipo">
                <option value="Seleccion Multiple">Seleccion Multiple</option>
                <option value="Verdadero/Falso">Verdadero/Falso</option>
            </select>
        </div>
        <div class="field">
            <label>Pregunta <span class="req">*</span></label>
            <textarea name="pregunta" id="pregunta"></textarea>
        </div>

        <div id="respuestas-container"></div>

        <br>
        <button class="btn btn-primary">Guardar pregunta</button>
    </form>
</div>

<script>
(function() {
    var tipos = {
        'Seleccion Multiple': 4,
        'Verdadero/Falso': 2
    };
    var textos = {
        'Seleccion Multiple': ['Respuesta 1', 'Respuesta 2', 'Respuesta 3', 'Respuesta 4'],
        'Verdadero/Falso': ['Verdadero', 'Falso']
    };

    function renderRespuestas(tipo) {
        var container = document.getElementById('respuestas-container');
        var count = tipos[tipo] || 4;
        var labels = textos[tipo] || textos['Seleccion Multiple'];
        container.innerHTML = '';

        for (var i = 0; i < count; i++) {
            var div = document.createElement('div');
            div.className = 'field';

            var label = document.createElement('label');
            label.textContent = labels[i] + ' *';
            div.appendChild(label);

            var input = document.createElement('input');
            input.type = 'text';
            input.name = 'respuesta[]';
            input.value = labels[i];
            div.appendChild(input);

            var correctaLabel = document.createElement('label');
            correctaLabel.style.marginLeft = '10px';
            var radio = document.createElement('input');
            radio.type = 'radio';
            radio.name = 'correcta';
            radio.value = i;
            if (i === 0) radio.checked = true;
            correctaLabel.appendChild(radio);
            correctaLabel.append(' Correcta');
            div.appendChild(correctaLabel);

            container.appendChild(div);
        }
    }

    var tipoSelect = document.getElementById('tipo');
    renderRespuestas(tipoSelect.value);
    tipoSelect.addEventListener('change', function() {
        renderRespuestas(this.value);
    });
})();
</script>

<?php require __DIR__ . '/../partials/footer.php'; ?>

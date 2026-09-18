<?php
$pageTitle = 'Certificado - ' . $certificado['titulo_curso'];
$pageSubtitle = 'Certificado de finalización';
require __DIR__ . '/../partials/header.php';
?>
<div class="certificado-page">
    <div class="certificado">
        <div class="certificado-header">
            <h1>Certificado de Finalización</h1>
            <p class="sello">MagisCode</p>
        </div>

        <div class="certificado-body">
            <p class="texto">Se certifica que</p>
            <h2 class="nombre"><?= htmlspecialchars($certificado['nombre'] . ' ' . $certificado['apellido']) ?></h2>
            <p class="texto">ha completado satisfactoriamente el curso de</p>
            <h3 class="curso"><?= htmlspecialchars($certificado['titulo_curso']) ?></h3>
            <p class="texto">con una calificación aprobatoria en todas las evaluaciones.</p>
            <p class="fecha">Fecha de emisión: <?= date('d/m/Y', strtotime($certificado['fecha_emision'])) ?></p>
        </div>

        <div class="certificado-footer">
            <p class="codigo">Código de validación: <?= htmlspecialchars($certificado['codigo_validacion']) ?></p>
        </div>
    </div>

    <div class="certificado-actions">
        <a class="btn btn-primary" href="index.php?route=descargar-certificado&id=<?=$certificado['id_certificado']?>&curso=<?=$certificado['id_curso_ce']?>">
            Descargar certificado PDF
        </a>
        <a class="btn btn-secondary" href="index.php?route=certificados">← Volver a mis certificados</a>
    </div>
</div>

<style>
.certificado-page { max-width: 900px; margin: 0 auto; padding: 30px 20px; }
.certificado {
    background: #fff;
    border: 12px double #0878df;
    border-radius: 16px;
    padding: 40px;
    box-shadow: 0 20px 60px rgba(0,0,0,.15);
    position: relative;
    margin-bottom: 30px;
}
.certificado::before {
    content: '';
    position: absolute;
    top: 16px;
    left: 16px;
    right: 16px;
    bottom: 16px;
    border: 4px solid #0878df;
    border-radius: 12px;
    pointer-events: none;
}
.certificado-header { text-align: center; margin-bottom: 30px; }
.certificado-header h1 { font-size: 28px; margin: 0; color: #0878df; }
.sello { font-size: 20px; font-weight: bold; color: #555; margin-top: 10px; }
.certificado-body { text-align: center; padding: 20px; }
.texto { font-size: 16px; color: #333; line-height: 1.8; margin: 0; }
.nombre { font-size: 28px; font-weight: bold; color: #10213b; margin: 15px 0; }
.curso { font-size: 22px; font-weight: 600; color: #0878df; margin: 10px 0; }
.fecha { font-size: 14px; color: #666; margin-top: 20px; }
.certificado-footer { text-align: center; margin-top: 30px; border-top: 1px dashed #ccc; padding-top: 15px; }
.codigo { font-size: 13px; color: #888; }
.certificado-actions { text-align: center; display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; }
@media(print) {
    .certificado-actions { display: none; }
    .certificado-page { padding: 0; }
    .certificado { box-shadow: none; border: 12px double #0878df; }
}
</style>
<?php require __DIR__ . '/../partials/footer.php'; ?>

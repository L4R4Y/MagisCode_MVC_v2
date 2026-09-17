<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>MagisCode - Iniciar sesión</title>
<link rel="stylesheet" href="assets/css/app.css">
</head>
<body>
<div style="min-height:100vh;display:grid;place-items:center;background:#f4f7fb;padding:20px">
    <div class="card" style="width:430px">
        <div class="logo" style="justify-content:center;color:#10213b;margin:0 0 20px">
            <img src="assets/img/magiscode-logo.png" alt="MagisCode">
            <span>Magis<span>Code</span></span>
        </div>
        <h2>Iniciar sesión</h2>
        <p>Accede a tu cuenta para continuar.</p>
        <?php if (!empty($error)): ?><div class="alert"><?= htmlspecialchars($error) ?></div><?php endif; ?>
        <p class="form-note">Los campos marcados con <span class="req">*</span> son obligatorios.</p>

        <form method="post" action="index.php?route=login">
            <div class="field">
                <label>Usuario <span class="req">*</span></label>
                <input name="usuario" required autocomplete="username">
            </div>
            <br>
            <div class="field">
                <label>Contraseña <span class="req">*</span></label>
                <input type="password" name="password" required autocomplete="current-password">
            </div>
            <br>
            <button class="btn btn-primary" style="width:100%" type="submit">Ingresar</button>
        </form>
    </div>
</div>
</body>
</html>

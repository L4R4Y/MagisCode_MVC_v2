MAGISCODE - MVC + PHP + MySQL

1. Copia la carpeta MagisCode_MVC dentro de C:\xampp\htdocs\
2. Usa la base de datos magiscode existente.
3. ANTES de probar el login en esta versión, ejecuta UNA VEZ:
   database/migracion_hashes_prueba.sql
   en MySQL Workbench sobre la base magiscode.
4. Luego abre:
   http://localhost/MagisCode_MVC/

CUENTAS DE PRUEBA
- Administrador: admin / hash_admin
- Instructor: jperez / hash123
- Aprendiz: cramirez / hash123

SEGURIDAD DEL LOGIN
- La contraseña se verifica exclusivamente con password_verify().
- Las nuevas contraseñas se almacenan con password_hash().
- Después de 5 intentos fallidos, la cuenta pasa a estado Bloqueado.
- Un acceso correcto reinicia intentos_fallidos a 0.
- No se acepta ninguna contraseña almacenada en texto plano.

IMPORTANTE
El archivo database/migracion_hashes_prueba.sql solo se ejecuta una vez para convertir las cuentas de prueba que vienen en el dump original.

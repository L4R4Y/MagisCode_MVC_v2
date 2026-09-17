-- MIGRACIÓN: convierte a hash real las contraseñas de prueba del dump original.
--
-- El dump (Dump20260907.sql) trae la columna hash_contrasena con texto plano
-- ('hash_admin', 'hash123', 'hash987', 'hashh765') en vez de un hash bcrypt.
-- El login usa exclusivamente password_verify(), así que con el dato tal cual
-- viene NINGUNA cuenta de prueba puede iniciar sesión.
--
-- Este script reemplaza esos textos por el hash bcrypt real de la misma
-- contraseña, para que sigas iniciando sesión con las mismas credenciales
-- documentadas en README.txt (ej: admin / hash_admin).
--
-- Ejecutar UNA SOLA VEZ sobre la base `magiscode`, antes de probar el login.
-- Si ya corriste esto antes con otro archivo, NO lo vuelvas a correr
-- (dejaría los hashes ya convertidos sin cambios porque no quedará ningún
-- registro con el texto plano original).

USE magiscode;

UPDATE usuario
SET hash_contrasena = '$2y$10$9PsI/RsxzSYOhsNUSq365ufE4DXbl8jekrlvKoYbJEg18Q/y7egcS'
WHERE hash_contrasena = 'hash_admin';

UPDATE usuario
SET hash_contrasena = '$2y$10$nEUIO.aHyeSI7UOcOCCKSeb5GoNOOQZXAV9mqeO8rp1xKDVuS9hqe'
WHERE hash_contrasena = 'hash123';

UPDATE usuario
SET hash_contrasena = '$2y$10$C/.7CJed/cTbDOlydpvqSeQ7Zjw8HNHxUajIbS1eSRVTFfeStfF.u'
WHERE hash_contrasena = 'hash987';

UPDATE usuario
SET hash_contrasena = '$2y$10$zgzSQMAv4MxLX657CAPlAOCyjdFpHXP8dGheDE7F2uEHCX1K6MT6S'
WHERE hash_contrasena = 'hashh765';

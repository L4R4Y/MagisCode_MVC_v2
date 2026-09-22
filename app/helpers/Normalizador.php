<?php

final class Normalizador
{
    public static function texto(string $valor): string
    {
        return mb_strtolower(trim($valor), 'UTF-8');
    }

    public static function campos(array $datos, array $campos): array
    {
        foreach ($campos as $campo) {
            if (array_key_exists($campo, $datos) && is_string($datos[$campo])) {
                $datos[$campo] = self::texto($datos[$campo]);
            }
        }

        return $datos;
    }
}

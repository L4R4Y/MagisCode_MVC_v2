<?php

class VideoInfo
{
    public static function duracionSegundos(string $rutaArchivo): ?int
    {
        if (!is_file($rutaArchivo)) {
            return null;
        }

        $extension = strtolower(pathinfo($rutaArchivo, PATHINFO_EXTENSION));
        if ($extension !== 'mp4') {
            return null;
        }

        $fp = fopen($rutaArchivo, 'rb');
        if ($fp === false) {
            return null;
        }

        $duracion = null;

        while (!feof($fp)) {
            $header = fread($fp, 8);
            if (strlen($header) < 8) {
                break;
            }

            $sizeData = unpack('N', substr($header, 0, 4))[1];
            $type = substr($header, 4, 4);

            if ($sizeData === 0) {
                break;
            }

            if ($sizeData === 1) {
                $extended = fread($fp, 8);
                if (strlen($extended) < 8) {
                    break;
                }
                $sizeData = (int) (unpack('J', substr($extended, 0, 8))[1] ?? 0);
                if ($sizeData === 0) {
                    $resto = stream_get_contents($fp);
                    fclose($fp);
                    if ($type !== 'moov') {
                        return null;
                    }
                    return self::extraerDuracionDeMoov($resto);
                }
            }

            if ($type === 'moov') {
                $data = fread($fp, $sizeData - 8);
                if (strlen($data) < $sizeData - 8) {
                    break;
                }
                $duracion = self::extraerDuracionDeMoov($data);
                break;
            }

            if ($sizeData > 8) {
                fseek($fp, $sizeData - 8, SEEK_CUR);
            }
        }

        fclose($fp);

        return $duracion;
    }

    private static function extraerDuracionDeMoov(string $data): ?int
    {
        $pos = 0;
        $len = strlen($data);

        while ($pos + 8 <= $len) {
            $boxSize = unpack('N', substr($data, $pos, 4))[1];
            $boxType = substr($data, $pos + 4, 4);

            if ($boxSize === 0) {
                break;
            }

            if ($boxSize === 1) {
                if ($pos + 16 > $len) {
                    break;
                }
                $boxSize = (int) (unpack('J', substr($data, $pos + 8, 8))[1] ?? 0);
            }

            if ($boxSize < 8 || $pos + $boxSize > $len) {
                break;
            }

            if ($boxType === 'mvhd') {
                return self::leerMvhd(substr($data, $pos + 8, $boxSize - 8));
            }

            $pos += $boxSize;
        }

        return null;
    }

    private static function leerMvhd(string $data): ?int
    {
        if (strlen($data) < 20) {
            return null;
        }

        $version = ord($data[0]);

        if ($version === 1) {
            if (strlen($data) < 44) {
                return null;
            }
            $timescale = unpack('N', substr($data, 24, 4))[1];
            $durationHi = unpack('N', substr($data, 32, 4))[1];
            $durationLo = unpack('N', substr($data, 36, 4))[1];
            $duration = ($durationHi << 32) | $durationLo;
        } else {
            $timescale = unpack('N', substr($data, 12, 4))[1];
            $duration = unpack('N', substr($data, 16, 4))[1];
        }

        if ($timescale <= 0) {
            return null;
        }

        return (int) round($duration / $timescale);
    }

    public static function formatearDuracion(int $segundos): string
    {
        $horas = (int) floor($segundos / 3600);
        $minutos = (int) floor(($segundos % 3600) / 60);
        $seg = $segundos % 60;

        if ($horas > 0) {
            return sprintf('%dh %dm %ds', $horas, $minutos, $seg);
        }

        if ($minutos > 0) {
            return sprintf('%dm %ds', $minutos, $seg);
        }

        return sprintf('%ds', $seg);
    }
}

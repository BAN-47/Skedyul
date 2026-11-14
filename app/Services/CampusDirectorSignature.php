<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CampusDirectorSignature
{
    public static function write(Worksheet $sheet, string $name, ?string $fallbackCell = null): void
    {
        $targetCell = null;

        foreach ($sheet->getCellCollection()->getCoordinates() as $coordinate) {
            $value = trim((string) $sheet->getCell($coordinate)->getValue());
            if (mb_strtolower($value) !== 'campus director') {
                continue;
            }

            [$column, $row] = Coordinate::coordinateFromString($coordinate);
            if ((int) $row > 1) {
                $targetCell = $column . ((int) $row - 1);
                break;
            }
        }

        $targetCell ??= $fallbackCell;
        if ($targetCell !== null) {
            $sheet->setCellValue($targetCell, mb_strtoupper(trim($name)));
        }
    }
}

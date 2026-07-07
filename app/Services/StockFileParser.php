<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use InvalidArgumentException;
use Maatwebsite\Excel\Facades\Excel;

class StockFileParser
{
    /**
     * @return array<int, array{stock: string, price: float, date: string}>
     */
    public function parse(UploadedFile $file): array
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $rows = match ($extension) {
            'csv' => $this->parseCsv($file->getRealPath()),
            'xls', 'xlsx', 'ods' => $this->parseSpreadsheet($file),
            default => throw new InvalidArgumentException('Unsupported file type. Please upload CSV, XLS, XLSX, or ODS.'),
        };

        return $this->normalizeRows($rows);
    }

    private function parseCsv(string $path): array
    {
        $rows = [];
        $handle = fopen($path, 'r');

        if ($handle === false) {
            throw new InvalidArgumentException('Unable to read the uploaded file.');
        }

        while (($row = fgetcsv($handle)) !== false) {
            $rows[] = $row;
        }

        fclose($handle);

        return $rows;
    }

    private function parseSpreadsheet(UploadedFile $file): array
    {
        $sheets = Excel::toArray(null, $file);

        return $sheets[0] ?? [];
    }

    /**
     * @param  array<int, array<int, mixed>>  $rows
     * @return array<int, array{stock: string, price: float, date: string}>
     */
    private function normalizeRows(array $rows): array
    {
        if ($rows === []) {
            throw new InvalidArgumentException('The uploaded file is empty.');
        }

        $header = array_map(
            fn ($value) => strtolower(trim((string) $value)),
            $rows[0]
        );

        $stockIndex = $this->columnIndex($header, ['stock']);
        $priceIndex = $this->columnIndex($header, ['price']);
        $dateIndex = $this->columnIndex($header, ['date']);

        $hasHeader = $stockIndex !== null && $priceIndex !== null && $dateIndex !== null;
        $startRow = $hasHeader ? 1 : 0;

        if (! $hasHeader) {
            $stockIndex = 0;
            $priceIndex = 1;
            $dateIndex = 2;
        }

        $records = [];

        for ($i = $startRow; $i < count($rows); $i++) {
            $row = $rows[$i];

            if (! is_array($row) || $this->rowIsEmpty($row)) {
                continue;
            }

            $stock = trim((string) ($row[$stockIndex] ?? ''));
            $price = $row[$priceIndex] ?? null;
            $date = trim((string) ($row[$dateIndex] ?? ''));

            if ($stock === '' || $price === null || $price === '' || $date === '') {
                continue;
            }

            if (! is_numeric($price)) {
                // throw new InvalidArgumentException("Invalid price value on row ".($i + 1).".");
                throw new InvalidArgumentException("Invalid headers or value on row ".($i + 1).". Please ensure the file has the correct headers and values: stock, price, date.");

            }

            $records[] = [
                'stock' => $stock,
                'price' => (float) $price,
                'date' => $this->normalizeDate($date, $i + 1),
            ];
        }

        if ($records === []) {
            throw new InvalidArgumentException('No valid stock price rows were found in the file.');
        }

        return $records;
    }

    /**
     * @param  array<int, string>  $header
     * @param  array<int, string>  $candidates
     */
    private function columnIndex(array $header, array $candidates): ?int
    {
        foreach ($candidates as $candidate) {
            $index = array_search($candidate, $header, true);

            if ($index !== false) {
                return $index;
            }
        }

        return null;
    }

    /**
     * @param  array<int, mixed>  $row
     */
    private function rowIsEmpty(array $row): bool
    {
        foreach ($row as $value) {
            if (trim((string) $value) !== '') {
                return false;
            }
        }

        return true;
    }

    private function normalizeDate(string $value, int $rowNumber): string
    {
        try {
            if (is_numeric($value)) {
                return Carbon::instance(
                    \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((float) $value)
                )->toDateString();
            }

            return Carbon::parse($value)->toDateString();
        } catch (\Throwable) {
            throw new InvalidArgumentException("Invalid date value on row {$rowNumber}.");
        }
    }
}
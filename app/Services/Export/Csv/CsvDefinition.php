<?php

namespace App\Services\Export\Csv;

interface CsvDefinition
{
    /**
     * CSVのヘッダ行（カラム名）を返す
     *
     * @return array<int, string>
     */
    public function headers(): array;

    /**
     * CSVのデータ行を返す（各行は配列）
     *
     * @return iterable<int, array<int, scalar|null>>
     */
    public function rows(): iterable;
}

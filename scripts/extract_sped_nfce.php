<?php

declare(strict_types=1);

if ($argc < 2) {
    fwrite(STDERR, "Uso: php scripts/extract_sped_nfce.php ARQUIVO_SPED [CSV_SAIDA]\n");
    exit(1);
}

$inputPath = $argv[1];
$outputPath = $argv[2] ?? preg_replace('/\.[^.]+$/', '', $inputPath) . '-nfce.csv';

if (!is_file($inputPath) || !is_readable($inputPath)) {
    fwrite(STDERR, "Arquivo não encontrado ou sem permissão de leitura: {$inputPath}\n");
    exit(1);
}

$fuelNames = [
    'GASOLINA COMUM',
    'GAS. ADITIVADA',
    'ETANOL',
    'DIESEL S500',
    'DIESEL S10',
    'ARLA 32 - VENDA BOMBA',
];

$handle = fopen($inputPath, 'rb');
if ($handle === false) {
    fwrite(STDERR, "Não foi possível abrir o arquivo de entrada.\n");
    exit(1);
}

$rows = [];
$currentDoc = null;
$totalC100 = 0;
$totalNfce = 0;
$nfceWithItems = 0;
$nfceWithFuelItems = 0;

while (($line = fgets($handle)) !== false) {
    $line = trim($line);
    if ($line === '') {
        continue;
    }

    $parts = explode('|', $line);
    $record = $parts[1] ?? '';

    if ($record === 'C100') {
        if ($currentDoc !== null && $currentDoc['modelo'] === '65') {
            $rows[] = finalizeRow($currentDoc);
        }

        $totalC100++;
        $currentDoc = [
            'ind_oper' => $parts[2] ?? '',
            'ind_emit' => $parts[3] ?? '',
            'cod_part' => $parts[4] ?? '',
            'modelo' => $parts[5] ?? '',
            'cod_sit' => $parts[6] ?? '',
            'serie' => $parts[7] ?? '',
            'num_doc' => $parts[8] ?? '',
            'chave' => $parts[9] ?? '',
            'dt_doc' => $parts[10] ?? '',
            'dt_e_s' => $parts[11] ?? '',
            'vl_doc' => $parts[12] ?? '',
            'has_items' => false,
            'fuel_items' => [],
        ];

        if ($currentDoc['modelo'] === '65') {
            $totalNfce++;
        }

        continue;
    }

    if ($currentDoc === null || $currentDoc['modelo'] !== '65') {
        continue;
    }

    if ($record !== 'C170') {
        continue;
    }

    $currentDoc['has_items'] = true;
    $itemCode = $parts[3] ?? '';
    $description = $parts[4] !== '' ? ($parts[4] ?? '') : ($parts[3] ?? '');
    $quantity = normalizeNumber($parts[5] ?? '');
    $unit = $parts[6] ?? '';
    $itemValue = normalizeNumber($parts[7] ?? '');

    if (in_array($description, $fuelNames, true)) {
        $currentDoc['fuel_items'][] = [
            'code' => $itemCode,
            'description' => $description,
            'quantity' => $quantity,
            'unit' => $unit,
            'value' => $itemValue,
        ];
    }
}

fclose($handle);

if ($currentDoc !== null && $currentDoc['modelo'] === '65') {
    $rows[] = finalizeRow($currentDoc);
}

$out = fopen((string) $outputPath, 'wb');
if ($out === false) {
    fwrite(STDERR, "Não foi possível criar o CSV de saída: {$outputPath}\n");
    exit(1);
}

fputcsv($out, [
    'chave',
    'numero',
    'serie',
    'cod_sit',
    'data',
    'valor_total',
    'tem_itens',
    'tem_combustivel',
    'combustiveis',
    'litros_total',
    'valor_itens_combustivel',
]);

foreach ($rows as $row) {
    fputcsv($out, $row);
    if ($row['tem_itens'] === 'sim') {
        $nfceWithItems++;
    }
    if ($row['tem_combustivel'] === 'sim') {
        $nfceWithFuelItems++;
    }
}

fclose($out);

echo json_encode([
    'input' => $inputPath,
    'output_csv' => $outputPath,
    'total_c100' => $totalC100,
    'total_nfce_modelo_65' => $totalNfce,
    'nfce_com_c170' => $nfceWithItems,
    'nfce_com_combustivel_e_litros' => $nfceWithFuelItems,
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . PHP_EOL;

function finalizeRow(array $doc): array
{
    $fuelNames = [];
    $liters = 0.0;
    $fuelValue = 0.0;

    foreach ($doc['fuel_items'] as $item) {
        $fuelNames[] = $item['description'];
        if ($item['unit'] === 'L') {
            $liters += $item['quantity'];
        }
        $fuelValue += $item['value'];
    }

    return [
        'chave' => $doc['chave'],
        'numero' => $doc['num_doc'],
        'serie' => $doc['serie'],
        'cod_sit' => $doc['cod_sit'],
        'data' => formatDate($doc['dt_doc']),
        'valor_total' => $doc['vl_doc'],
        'tem_itens' => $doc['has_items'] ? 'sim' : 'nao',
        'tem_combustivel' => $doc['fuel_items'] !== [] ? 'sim' : 'nao',
        'combustiveis' => implode('; ', array_values(array_unique($fuelNames))),
        'litros_total' => $liters > 0 ? number_format($liters, 3, '.', '') : '',
        'valor_itens_combustivel' => $fuelValue > 0 ? number_format($fuelValue, 2, '.', '') : '',
    ];
}

function normalizeNumber(string $value): float
{
    if ($value === '') {
        return 0.0;
    }
    return (float) str_replace(',', '.', $value);
}

function formatDate(string $value): string
{
    if (strlen($value) !== 8) {
        return $value;
    }
    return substr($value, 4, 4) . '-' . substr($value, 2, 2) . '-' . substr($value, 0, 2);
}

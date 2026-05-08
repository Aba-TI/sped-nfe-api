<?php

declare(strict_types=1);

use App\Exceptions\FiscalException;
use App\Services\Fiscal\CertificateService;

require dirname(__DIR__) . '/bootstrap.php';

$inputPath = $argv[1] ?? null;
$outputPath = $argv[2] ?? null;
$inputPassword = $argv[3] ?? null;
$outputPassword = $argv[4] ?? null;

if ($inputPath === null || $outputPath === null || $inputPassword === null) {
    fwrite(STDERR, "Uso:\n");
    fwrite(STDERR, "php console/repack-certificate.php <input.pfx> <output.pfx> <senha-origem> [senha-destino]\n");
    exit(1);
}

$service = new CertificateService();

try {
    $result = $service->repackPfxWithOpenSsl(
        $inputPath,
        $inputPassword,
        $outputPath,
        $outputPassword
    );

    fwrite(STDOUT, "Certificado reempacotado com sucesso.\n");
    fwrite(STDOUT, "Origem: {$result['input_path']}\n");
    fwrite(STDOUT, "Destino: {$result['output_path']}\n");

    if ($result['info'] !== '') {
        fwrite(STDOUT, "\nResumo do openssl:\n{$result['info']}\n");
    }

    fwrite(STDOUT, "\nObservacao: isso nao renova nem atualiza juridicamente o certificado.\n");
    exit(0);
} catch (FiscalException $e) {
    fwrite(STDERR, "Erro: {$e->getMessage()}\n");

    $details = $e->details();
    if ($details !== []) {
        fwrite(STDERR, json_encode($details, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n");
    }

    exit(1);
} catch (Throwable $e) {
    fwrite(STDERR, "Erro inesperado: {$e->getMessage()}\n");
    exit(1);
}

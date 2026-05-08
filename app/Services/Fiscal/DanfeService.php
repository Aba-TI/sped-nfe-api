<?php

declare(strict_types=1);

namespace App\Services\Fiscal;

use App\Exceptions\FiscalException;

final class DanfeService
{
    /**
     * @return array<string, mixed>
     */
    public function generate(string $xml): array
    {
        if (!class_exists(\NFePHP\DA\NFe\Danfe::class)) {
            throw new FiscalException(
                'Geração de DANFE depende do pacote nfephp-org/sped-da, que não está instalado neste projeto.',
                'DANFE_DEPENDENCY_MISSING',
                ['composer_require' => 'nfephp-org/sped-da'],
                501
            );
        }

        $danfe = new \NFePHP\DA\NFe\Danfe($xml);
        $pdf = $danfe->render();

        return [
            'pdf_base64' => base64_encode($pdf),
            'content_type' => 'application/pdf',
        ];
    }
}

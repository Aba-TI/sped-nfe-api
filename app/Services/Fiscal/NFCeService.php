<?php

declare(strict_types=1);

namespace App\Services\Fiscal;

use App\DTO\NFCeRequestDTO;

final class NFCeService
{
    private const MODEL = 65;

    private SefazService $sefazService;

    public function __construct(SefazService $sefazService)
    {
        $this->sefazService = $sefazService;
    }

    /**
     * @return array<string, mixed>
     */
    public function validarCertificado(NFCeRequestDTO $dto): array
    {
        return $this->sefazService->validateCertificate($dto->payload(), self::MODEL);
    }

    /**
     * @return array<string, mixed>
     */
    public function criar(NFCeRequestDTO $dto): array
    {
        return $this->sefazService->create($dto->payload(), self::MODEL);
    }

    /**
     * @return array<string, mixed>
     */
    public function assinar(NFCeRequestDTO $dto): array
    {
        return $this->sefazService->sign($dto->payload(), self::MODEL);
    }

    /**
     * @return array<string, mixed>
     */
    public function enviar(NFCeRequestDTO $dto): array
    {
        return $this->sefazService->send($dto->payload(), self::MODEL);
    }

    /**
     * @return array<string, mixed>
     */
    public function consultar(string $chave, NFCeRequestDTO $dto): array
    {
        return $this->sefazService->consultByKey($dto->payload(), self::MODEL, $chave);
    }

    /**
     * @return array<string, mixed>
     */
    public function status(NFCeRequestDTO $dto): array
    {
        return $this->sefazService->status($dto->payload(), self::MODEL);
    }

    /**
     * @return array<string, mixed>
     */
    public function cancelar(string $chave, NFCeRequestDTO $dto): array
    {
        return $this->sefazService->cancel($dto->payload(), self::MODEL, $chave);
    }

    /**
     * @return array<string, mixed>
     */
    public function danfe(string $chave, NFCeRequestDTO $dto): array
    {
        return $this->sefazService->danfe($dto->payload(), self::MODEL, $chave);
    }

    /**
     * @return array<string, mixed>
     */
    public function qrCode(NFCeRequestDTO $dto): array
    {
        return $this->sefazService->qrCode($dto->payload());
    }
}

<?php

declare(strict_types=1);

namespace App\Controllers;

use App\DTO\NFCeRequestDTO;
use App\Http\JsonResponse;
use App\Http\Request;
use App\Services\Fiscal\NFCeService;

final class NFCeController
{
    private NFCeService $service;

    public function __construct(NFCeService $service)
    {
        $this->service = $service;
    }

    public function validarCertificado(Request $request): JsonResponse
    {
        return $this->success('Certificado validado com sucesso.', $this->service->validarCertificado(NFCeRequestDTO::fromArray($request->body())));
    }

    public function criar(Request $request): JsonResponse
    {
        return $this->success('NFC-e criada com sucesso.', $this->service->criar(NFCeRequestDTO::fromArray($request->body())));
    }

    public function assinar(Request $request): JsonResponse
    {
        return $this->success('NFC-e assinada com sucesso.', $this->service->assinar(NFCeRequestDTO::fromArray($request->body())));
    }

    public function enviar(Request $request): JsonResponse
    {
        return $this->success('NFC-e enviada com sucesso.', $this->service->enviar(NFCeRequestDTO::fromArray($request->body())));
    }

    public function consultar(Request $request): JsonResponse
    {
        return $this->success(
            'Consulta da NFC-e realizada com sucesso.',
            $this->service->consultar((string) $request->attribute('chave'), NFCeRequestDTO::fromArray($request->body()))
        );
    }

    public function status(Request $request): JsonResponse
    {
        return $this->success('Status do serviço consultado com sucesso.', $this->service->status(NFCeRequestDTO::fromArray($request->body())));
    }

    public function cancelar(Request $request): JsonResponse
    {
        return $this->success(
            'NFC-e cancelada com sucesso.',
            $this->service->cancelar((string) $request->attribute('chave'), NFCeRequestDTO::fromArray($request->body()))
        );
    }

    public function danfe(Request $request): JsonResponse
    {
        return $this->success(
            'DANFE NFC-e gerado com sucesso.',
            $this->service->danfe((string) $request->attribute('chave'), NFCeRequestDTO::fromArray($request->body()))
        );
    }

    public function qrcode(Request $request): JsonResponse
    {
        return $this->success('QR Code gerado com sucesso.', $this->service->qrCode(NFCeRequestDTO::fromArray($request->body())));
    }

    /**
     * @param array<string, mixed> $data
     */
    private function success(string $message, array $data): JsonResponse
    {
        return new JsonResponse(200, [
            'success' => true,
            'message' => $message,
            'data' => $data,
        ]);
    }
}

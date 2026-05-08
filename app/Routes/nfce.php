<?php

declare(strict_types=1);

use App\Controllers\NFCeController;
use App\Http\Router;

return static function (Router $router, NFCeController $controller): void {
    $router->post('/api/nfce/certificado/validar', [$controller, 'validarCertificado']);
    $router->post('/api/nfce/criar', [$controller, 'criar']);
    $router->post('/api/nfce/assinar', [$controller, 'assinar']);
    $router->post('/api/nfce/enviar', [$controller, 'enviar']);
    $router->post('/api/nfce/qrcode', [$controller, 'qrcode']);
    $router->get('/api/nfce/status-servico', [$controller, 'status']);
    $router->post('/api/nfce/status-servico', [$controller, 'status']);
    $router->get('/api/nfce/{chave}/consultar', [$controller, 'consultar']);
    $router->post('/api/nfce/{chave}/consultar', [$controller, 'consultar']);
    $router->post('/api/nfce/{chave}/cancelar', [$controller, 'cancelar']);
    $router->get('/api/nfce/{chave}/danfe', [$controller, 'danfe']);
    $router->post('/api/nfce/{chave}/danfe', [$controller, 'danfe']);
};

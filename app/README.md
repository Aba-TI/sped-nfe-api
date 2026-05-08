## Camada de Integração Fiscal

Esta pasta contém a ponte do sistema com a `nfephp-org/sped-nfe`.

Princípios:

- `src/` continua sendo a biblioteca, sem customização.
- `App\Adapters\SpedNFeAdapter` é o único ponto que usa classes `NFePHP\...`.
- `App\Services\Fiscal\...` concentra regras de negócio, validações e fluxos.
- `App\Controllers\...` apenas traduz HTTP para serviço.
- `app/Routes/*.php` mantém o mapa de rotas separado.

Limite atual deste workspace:

- Não existe repositório real de empresas/certificados/notas.
- Se você enviar apenas `empresa_id`, a camada retorna erro `COMPANY_REPOSITORY_NOT_IMPLEMENTED`.
- Para uso imediato, envie `emitente`, `certificado`, `ide`, `produtos`, `totais` e demais blocos no payload.

## Reempacotar certificado `.pfx`

Se você precisa gerar outro arquivo `.pfx` a partir de um já existente, use o script:

```bash
php console/repack-certificate.php \
  ~/Downloads/'SOLUTIONS TRIBUTARY LTDA21210585000144(1).pfx' \
  ./certs/certificado-novo.pfx \
  'SENHA_ATUAL' \
  'SENHA_NOVA'
```

Se quiser manter a mesma senha no novo arquivo, basta omitir a senha final:

```bash
php console/repack-certificate.php \
  ~/Downloads/'SOLUTIONS TRIBUTARY LTDA21210585000144(1).pfx' \
  ./certs/certificado-novo.pfx \
  'SENHA_ATUAL'
```

Observações:

- Esse processo reempacota o `.pfx` com `openssl`.
- Isso não renova, não atualiza a validade e não cria um certificado novo juridicamente.
- O diretório de destino, como `./certs`, precisa existir e permitir escrita.

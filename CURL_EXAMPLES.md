# Exemplos de `curl` das Rotas Fiscais

Base URL usada nos exemplos:

```bash
http://localhost:${API_PORT:-8080}
```

Observações:

- Os exemplos abaixo foram reduzidos ao formato mais prático para uso manual.
- Quando a rota exige certificado, prefira `multipart/form-data` com:
  `-F 'payload={...};type=application/json'`
  `-F "certificado=@$CERT_PATH"`
  `-F "certificado_password=$CERT_PASSWORD"`
- Se algum trecho abaixo ainda mostrar `certificado.path`, considere esse formato legado e use o padrão multipart acima.
- O campo `ambiente` aceita `producao`/`1` ou `homologacao`/`2`. Não existe um terceiro ambiente separado chamado `teste`.
- Campos fiscais obrigatórios para autorização continuam no payload de criação.
- Consultas e status foram simplificados para evitar envio de dados que normalmente não agregam no uso diário.
- Na NFC-e, `CSC` e `CSCid` foram mantidos apenas nos exemplos em que fazem falta para emissão/assinatura/QR Code.
- Neste workspace, enviar apenas `empresa_id` ainda retorna `COMPANY_REPOSITORY_NOT_IMPLEMENTED`.
- Para DANFE via `POST`, a implementação atual exige envio do `xml` autorizado no body.

## Variáveis úteis

```bash
API_PORT="${API_PORT:-8080}"
BASE_URL="http://localhost:${API_PORT}"
ORIGIN="http://localhost:3000"
API_KEY="chave-front-3000"
CHAVE_NFE="35240212345678000199550010000001234567890123"
CHAVE_NFCE="35240212345678000199650010000001234567890123"
PROTOCOLO="135240000000000"
RECIBO="352400000000000"
CERT_PATH="/caminho/certificado.pfx"
CERT_PASSWORD="sua-senha"
```

Todas as rotas protegidas abaixo devem receber a `API key` compatível com o `Origin`.
Exemplo no `.env`: `API_ORIGIN_KEYS=http://localhost:3000=chave-front-3000,http://localhost:5173=chave-front-5173`.

## Health

```bash
curl -X GET "$BASE_URL/health"
```

## NF-e

### Validar certificado via upload de arquivo

```bash
curl -X POST "$BASE_URL/api/nfe/certificado/validar" \
  -H "Origin: $ORIGIN" \
  -H "X-API-Key: $API_KEY" \
  -F 'payload={
    "uf":"MG",
    "emitente":{
      "cnpj":"12345678000199",
      "razao_social":"EMPRESA EXEMPLO LTDA",
      "ie":"123456789",
      "crt":"3",
      "endereco":{
        "logradouro":"Rua Um",
        "numero":"100",
        "bairro":"Centro",
        "codigo_municipio":"3106200",
        "municipio":"Belo Horizonte",
        "uf":"MG",
        "cep":"30110000",
        "telefone":"3133334444"
      }
    }
  };type=application/json' \
  -F "certificado=@$CERT_PATH" \
  -F "certificado_password=$CERT_PASSWORD"
```

### Criar NF-e

```bash
curl -X POST "$BASE_URL/api/nfe/criar" \
  -H "Origin: $ORIGIN" \
  -H "X-API-Key: $API_KEY" \
  -F 'payload={
    "ambiente": "homologacao",
    "uf": "MG",
    "serie": 1,
    "numero": 123,
    "emitente": {
      "cnpj": "12345678000199",
      "razao_social": "EMPRESA EXEMPLO LTDA",
      "nome_fantasia": "EMPRESA EXEMPLO",
      "ie": "123456789",
      "crt": "3",
      "endereco": {
        "logradouro": "Rua Um",
        "numero": "100",
        "bairro": "Centro",
        "codigo_municipio": "3106200",
        "municipio": "Belo Horizonte",
        "uf": "MG",
        "cep": "30110000",
        "telefone": "3133334444"
      }
    },
    "ide": {
      "natOp": "VENDA DE MERCADORIA",
      "tpNF": 1,
      "idDest": 1,
      "cMunFG": "3106200",
      "tpImp": 1,
      "tpEmis": 1,
      "finNFe": 1,
      "indFinal": 0,
      "indPres": 0,
      "procEmi": 0,
      "verProc": "app-fiscal-bridge-1.0"
    },
    "destinatario": {
      "xNome": "CLIENTE TESTE LTDA",
      "cnpj": "98765432000188",
      "indIEDest": 1,
      "IE": "9988776655",
      "email": "cliente@exemplo.com",
      "endereco": {
        "logradouro": "Rua Dois",
        "numero": "200",
        "bairro": "Centro",
        "codigo_municipio": "3550308",
        "municipio": "Sao Paulo",
        "uf": "SP",
        "cep": "01001000",
        "telefone": "1133334444"
      }
    },
    "produtos": [
      {
        "cProd": "001",
        "cEAN": "SEM GTIN",
        "xProd": "PRODUTO TESTE",
        "NCM": "61091000",
        "CFOP": "5102",
        "uCom": "UN",
        "qCom": "1.0000",
        "vUnCom": "100.00",
        "vProd": "100.00",
        "cEANTrib": "SEM GTIN",
        "uTrib": "UN",
        "qTrib": "1.0000",
        "vUnTrib": "100.00",
        "indTot": 1,
        "imposto": {
          "vTotTrib": "18.00",
          "icms": {
            "orig": "0",
            "CST": "00",
            "modBC": "3",
            "vBC": "100.00",
            "pICMS": "18.00",
            "vICMS": "18.00"
          },
          "pis": {
            "CST": "01",
            "vBC": "100.00",
            "pPIS": "1.65",
            "vPIS": "1.65"
          },
          "cofins": {
            "CST": "01",
            "vBC": "100.00",
            "pCOFINS": "7.60",
            "vCOFINS": "7.60"
          }
        }
      }
    ],
    "totais": {
      "icmsTot": {
        "vBC": "100.00",
        "vICMS": "18.00",
        "vICMSDeson": "0.00",
        "vFCP": "0.00",
        "vBCST": "0.00",
        "vST": "0.00",
        "vFCPST": "0.00",
        "vFCPSTRet": "0.00",
        "vProd": "100.00",
        "vFrete": "0.00",
        "vSeg": "0.00",
        "vDesc": "0.00",
        "vII": "0.00",
        "vIPI": "0.00",
        "vPIS": "1.65",
        "vCOFINS": "7.60",
        "vOutro": "0.00",
        "vNF": "100.00",
        "vTotTrib": "18.00"
      }
    },
    "transporte": {
      "modFrete": 9
    }
  };type=application/json' \
  -F "certificado=@$CERT_PATH" \
  -F "certificado_password=$CERT_PASSWORD"
```

### Assinar NF-e a partir de um XML

```bash
curl -X POST "$BASE_URL/api/nfe/assinar" \
  -H "Origin: $ORIGIN" \
  -H "X-API-Key: $API_KEY" \
  -F 'payload={
    "uf": "MG",
    "emitente": {
      "cnpj": "12345678000199",
      "razao_social": "EMPRESA EXEMPLO LTDA",
      "ie": "123456789",
      "crt": "3",
      "endereco": {
        "codigo_municipio": "3106200",
        "municipio": "Belo Horizonte",
        "uf": "MG"
      }
    },
    "xml": "<NFe>...</NFe>"
  };type=application/json' \
  -F "certificado=@$CERT_PATH" \
  -F "certificado_password=$CERT_PASSWORD"
```

### Validar XML

```bash
curl -X POST "$BASE_URL/api/nfe/validar-xml" \
  -H "Origin: $ORIGIN" \
  -H "X-API-Key: $API_KEY" \
  -F 'payload={
    "uf": "MG",
    "emitente": {
      "cnpj": "12345678000199",
      "razao_social": "EMPRESA EXEMPLO LTDA",
      "ie": "123456789",
      "crt": "3",
      "endereco": {
        "codigo_municipio": "3106200",
        "municipio": "Belo Horizonte",
        "uf": "MG"
      }
    },
    "xml": "<NFe>...</NFe>"
  };type=application/json' \
  -F "certificado=@$CERT_PATH" \
  -F "certificado_password=$CERT_PASSWORD"
```

### Enviar NF-e usando o XML no body

```bash
curl -X POST "$BASE_URL/api/nfe/enviar" \
  -H "Origin: $ORIGIN" \
  -H "X-API-Key: $API_KEY" \
  -F 'payload={
    "ambiente": "homologacao",
    "uf": "MG",
    "lote": "12345",
    "sincrono": 1,
    "emitente": {
      "cnpj": "12345678000199",
      "razao_social": "EMPRESA EXEMPLO LTDA",
      "ie": "123456789",
      "crt": "3",
      "endereco": {
        "codigo_municipio": "3106200",
        "municipio": "Belo Horizonte",
        "uf": "MG"
      }
    },
    "xml": "<NFe>...</NFe>"
  };type=application/json' \
  -F "certificado=@$CERT_PATH" \
  -F "certificado_password=$CERT_PASSWORD"
```

### Consultar protocolo por recibo

```bash
curl -X POST "$BASE_URL/api/nfe/consultar-protocolo" \
  -H "Origin: $ORIGIN" \
  -H "X-API-Key: $API_KEY" \
  -F 'payload={
    "ambiente": "homologacao",
    "uf": "MG",
    "recibo": "'"$RECIBO"'",
    "cnpj": "12345678000199"
  };type=application/json' \
  -F "certificado=@$CERT_PATH" \
  -F "certificado_password=$CERT_PASSWORD"
```

### Consultar status do serviço com GET

```bash
curl -X GET "$BASE_URL/api/nfe/status-servico"
```

### Consultar status do serviço com POST

```bash
curl -X POST "$BASE_URL/api/nfe/status-servico" \
  -H "Origin: $ORIGIN" \
  -H "X-API-Key: $API_KEY" \
  -F 'payload={
    "ambiente": "homologacao",
    "uf": "MG",
    "cnpj": "12345678000199"
  };type=application/json' \
  -F "certificado=@$CERT_PATH" \
  -F "certificado_password=$CERT_PASSWORD"
```

### Consultar NF-e por chave com GET

```bash
curl -X GET "$BASE_URL/api/nfe/$CHAVE_NFE/consultar"
```

### Consultar NF-e por chave com POST

```bash
curl -X POST "$BASE_URL/api/nfe/$CHAVE_NFE/consultar" \
  -H "Origin: $ORIGIN" \
  -H "X-API-Key: $API_KEY" \
  -F 'payload={
    "ambiente": "homologacao",
    "uf": "MG",
    "cnpj": "12345678000199"
  };type=application/json' \
  -F "certificado=@$CERT_PATH" \
  -F "certificado_password=$CERT_PASSWORD"
```

### Cancelar NF-e

```bash
curl -X POST "$BASE_URL/api/nfe/$CHAVE_NFE/cancelar" \
  -H "Origin: $ORIGIN" \
  -H "X-API-Key: $API_KEY" \
  -F 'payload={
    "ambiente": "homologacao",
    "uf": "MG",
    "protocolo": "135240000000000",
    "justificativa": "Cancelamento por erro de emissao",
    "emitente": {
      "cnpj": "12345678000199",
      "razao_social": "EMPRESA EXEMPLO LTDA",
      "ie": "123456789",
      "crt": "3",
      "endereco": {
        "codigo_municipio": "3106200",
        "municipio": "Belo Horizonte",
        "uf": "MG"
      }
    }
  };type=application/json' \
  -F "certificado=@$CERT_PATH" \
  -F "certificado_password=$CERT_PASSWORD"
```

### Inutilizar numeração

```bash
curl -X POST "$BASE_URL/api/nfe/inutilizar" \
  -H "Origin: $ORIGIN" \
  -H "X-API-Key: $API_KEY" \
  -F 'payload={
    "ambiente": "homologacao",
    "uf": "MG",
    "serie": 1,
    "numero_inicial": 200,
    "numero_final": 210,
    "justificativa": "Faixa nao utilizada por ajuste operacional",
    "ano": "24",
    "emitente": {
      "cnpj": "12345678000199",
      "razao_social": "EMPRESA EXEMPLO LTDA",
      "ie": "123456789",
      "crt": "3",
      "endereco": {
        "codigo_municipio": "3106200",
        "municipio": "Belo Horizonte",
        "uf": "MG"
      }
    }
  };type=application/json' \
  -F "certificado=@$CERT_PATH" \
  -F "certificado_password=$CERT_PASSWORD"
```

### Consultar cadastro

```bash
curl -X POST "$BASE_URL/api/nfe/consultar-cadastro" \
  -H "Origin: $ORIGIN" \
  -H "X-API-Key: $API_KEY" \
  -F 'payload={
    "ambiente": "homologacao",
    "uf": "MG",
    "uf_consulta": "MG",
    "cnpj": "12345678000199",
    "emitente": {
      "cnpj": "12345678000199",
      "razao_social": "EMPRESA EXEMPLO LTDA",
      "ie": "123456789",
      "crt": "3",
      "endereco": {
        "codigo_municipio": "3106200",
        "municipio": "Belo Horizonte",
        "uf": "MG"
      }
    }
  };type=application/json' \
  -F "certificado=@$CERT_PATH" \
  -F "certificado_password=$CERT_PASSWORD"
```

### Gerar DANFE com GET

```bash
curl -X GET "$BASE_URL/api/nfe/$CHAVE_NFE/danfe"
```

### Gerar DANFE com POST

```bash
curl -X POST "$BASE_URL/api/nfe/$CHAVE_NFE/danfe" \
  -H "Content-Type: application/json" \
  -d '{
    "xml": "<nfeProc>...</nfeProc>"
  }'
```

## NFC-e

### Validar certificado

```bash
curl -X POST "$BASE_URL/api/nfce/certificado/validar" \
  -H "Origin: $ORIGIN" \
  -H "X-API-Key: $API_KEY" \
  -F 'payload={
    "uf": "MG",
    "emitente": {
      "cnpj": "12345678000199",
      "razao_social": "EMPRESA EXEMPLO LTDA",
      "ie": "123456789",
      "crt": "1",
      "endereco": {
        "logradouro": "Rua Um",
        "numero": "100",
        "bairro": "Centro",
        "codigo_municipio": "3106200",
        "municipio": "Belo Horizonte",
        "uf": "MG",
        "cep": "30110000"
      }
    }
  };type=application/json' \
  -F "certificado=@$CERT_PATH" \
  -F "certificado_password=$CERT_PASSWORD"
```
    <!-- 
    "CSC": "SEUCSC",
    "CSCid": "000001"
    -->
### Criar NFC-e

```bash
curl -X POST "$BASE_URL/api/nfce/criar" \
  -H "Origin: $ORIGIN" \
  -H "X-API-Key: $API_KEY" \
  -F 'payload={
    "ambiente": "homologacao",
    "uf": "MG",
    "serie": 1,
    "numero": 456,
    "CSC": "SEUCSC",
    "CSCid": "000001",
    "emitente": {
      "cnpj": "12345678000199",
      "razao_social": "EMPRESA EXEMPLO LTDA",
      "nome_fantasia": "LOJA TESTE",
      "ie": "123456789",
      "crt": "1",
      "endereco": {
        "logradouro": "Rua Um",
        "numero": "100",
        "bairro": "Centro",
        "codigo_municipio": "3106200",
        "municipio": "Belo Horizonte",
        "uf": "MG",
        "cep": "30110000"
      }
    },
    "ide": {
      "natOp": "VENDA BALCAO",
      "tpNF": 1,
      "idDest": 1,
      "cMunFG": "3106200",
      "tpImp": 4,
      "tpEmis": 1,
      "finNFe": 1,
      "indFinal": 1,
      "indPres": 1,
      "procEmi": 0,
      "verProc": "app-fiscal-bridge-1.0"
    },
    "destinatario": {
      "xNome": "CONSUMIDOR FINAL",
      "cpf": "12345678909",
      "indIEDest": 9,
      "endereco": {
        "logradouro": "Rua Dois",
        "numero": "200",
        "bairro": "Centro",
        "codigo_municipio": "3106200",
        "municipio": "Belo Horizonte",
        "uf": "MG",
        "cep": "30120000"
      }
    },
    "produtos": [
      {
        "cProd": "001",
        "cEAN": "SEM GTIN",
        "xProd": "ITEM NFCe",
        "NCM": "22030000",
        "CFOP": "5102",
        "uCom": "UN",
        "qCom": "1.0000",
        "vUnCom": "12.00",
        "vProd": "12.00",
        "cEANTrib": "SEM GTIN",
        "uTrib": "UN",
        "qTrib": "1.0000",
        "vUnTrib": "12.00",
        "indTot": 1,
        "imposto": {
          "vTotTrib": "2.16",
          "icms": {
            "orig": "0",
            "CST": "00",
            "modBC": "3",
            "vBC": "12.00",
            "pICMS": "18.00",
            "vICMS": "2.16"
          },
          "pis": {
            "CST": "01",
            "vBC": "12.00",
            "pPIS": "1.65",
            "vPIS": "0.20"
          },
          "cofins": {
            "CST": "01",
            "vBC": "12.00",
            "pCOFINS": "7.60",
            "vCOFINS": "0.91"
          }
        }
      }
    ],
    "totais": {
      "icmsTot": {
        "vBC": "12.00",
        "vICMS": "2.16",
        "vICMSDeson": "0.00",
        "vFCP": "0.00",
        "vBCST": "0.00",
        "vST": "0.00",
        "vFCPST": "0.00",
        "vFCPSTRet": "0.00",
        "vProd": "12.00",
        "vFrete": "0.00",
        "vSeg": "0.00",
        "vDesc": "0.00",
        "vII": "0.00",
        "vIPI": "0.00",
        "vPIS": "0.20",
        "vCOFINS": "0.91",
        "vOutro": "0.00",
        "vNF": "12.00",
        "vTotTrib": "2.16"
      }
    },
    "transporte": {
      "modFrete": 9
    },
    "pagamento": {
      "vTroco": "0.00",
      "detalhes": [
        {
          "tPag": "01",
          "vPag": "12.00"
        }
      ]
    }
  };type=application/json' \
  -F "certificado=@$CERT_PATH" \
  -F "certificado_password=$CERT_PASSWORD"
```

### Assinar NFC-e

```bash
curl -X POST "$BASE_URL/api/nfce/assinar" \
  -H "Origin: $ORIGIN" \
  -H "X-API-Key: $API_KEY" \
  -F 'payload={
    "uf": "MG",
    "CSC": "SEUCSC",
    "CSCid": "000001",
    "emitente": {
      "cnpj": "12345678000199",
      "razao_social": "EMPRESA EXEMPLO LTDA",
      "ie": "123456789",
      "crt": "1",
      "endereco": {
        "codigo_municipio": "3106200",
        "municipio": "Belo Horizonte",
        "uf": "MG"
      }
    },
    "xml": "<NFe>...</NFe>"
  };type=application/json' \
  -F "certificado=@$CERT_PATH" \
  -F "certificado_password=$CERT_PASSWORD"
```

### Enviar NFC-e

```bash
curl -X POST "$BASE_URL/api/nfce/enviar" \
  -H "Origin: $ORIGIN" \
  -H "X-API-Key: $API_KEY" \
  -F 'payload={
    "ambiente": "homologacao",
    "uf": "MG",
    "CSC": "SEUCSC",
    "CSCid": "000001",
    "lote": "67890",
    "sincrono": 1,
    "emitente": {
      "cnpj": "12345678000199",
      "razao_social": "EMPRESA EXEMPLO LTDA",
      "ie": "123456789",
      "crt": "1",
      "endereco": {
        "codigo_municipio": "3106200",
        "municipio": "Belo Horizonte",
        "uf": "MG"
      }
    },
    "xml": "<NFe>...</NFe>"
  };type=application/json' \
  -F "certificado=@$CERT_PATH" \
  -F "certificado_password=$CERT_PASSWORD"
```

### Gerar QR Code da NFC-e

```bash
curl -X POST "$BASE_URL/api/nfce/qrcode" \
  -H "Origin: $ORIGIN" \
  -H "X-API-Key: $API_KEY" \
  -F 'payload={
    "uf": "MG",
    "CSC": "SEUCSC",
    "CSCid": "000001",
    "emitente": {
      "cnpj": "12345678000199",
      "razao_social": "EMPRESA EXEMPLO LTDA",
      "ie": "123456789",
      "crt": "1",
      "endereco": {
        "codigo_municipio": "3106200",
        "municipio": "Belo Horizonte",
        "uf": "MG"
      }
    },
    "xml": "<NFe>...</NFe>"
  };type=application/json' \
  -F "certificado=@$CERT_PATH" \
  -F "certificado_password=$CERT_PASSWORD"
```

### Consultar status do serviço com GET

```bash
curl -X GET "$BASE_URL/api/nfce/status-servico"
```

### Consultar status do serviço com POST
"CSC": "SEUCSC",
"CSCid": "000001",
```bash
curl -X POST "$BASE_URL/api/nfce/status-servico" \
  -H "Origin: $ORIGIN" \
  -H "X-API-Key: $API_KEY" \
  -F 'payload={
    "ambiente": "homologacao",
    "uf": "MG",
    "cnpj": "12345678000199"
  };type=application/json' \
  -F "certificado=@$CERT_PATH" \
  -F "certificado_password=$CERT_PASSWORD"
```

### Consultar NFC-e por chave com GET

```bash
curl -X GET "$BASE_URL/api/nfce/$CHAVE_NFCE/consultar"
```

### Consultar NFC-e por chave com POST
"CSC": "SEUCSC",
"CSCid": "000001",
```bash
curl -X POST "$BASE_URL/api/nfce/$CHAVE_NFCE/consultar" \
  -H "Origin: $ORIGIN" \
  -H "X-API-Key: $API_KEY" \
  -F 'payload={
    "ambiente": "homologacao",
    "uf": "MG",
    "cnpj": "12345678000199"
  };type=application/json' \
  -F "certificado=@$CERT_PATH" \
  -F "certificado_password=$CERT_PASSWORD"
```

### Cancelar NFC-e
"CSC": "SEUCSC",
"CSCid": "000001",
```bash
curl -X POST "$BASE_URL/api/nfce/$CHAVE_NFCE/cancelar" \
  -H "Origin: $ORIGIN" \
  -H "X-API-Key: $API_KEY" \
  -F 'payload={
    "ambiente": "homologacao",
    "uf": "MG",
    "protocolo": "135240000000000",
    "justificativa": "Cancelamento por erro operacional",
    "emitente": {
      "cnpj": "12345678000199",
      "razao_social": "EMPRESA EXEMPLO LTDA",
      "ie": "123456789",
      "crt": "1",
      "endereco": {
        "codigo_municipio": "3106200",
        "municipio": "Belo Horizonte",
        "uf": "MG"
      }
    }
  };type=application/json' \
  -F "certificado=@$CERT_PATH" \
  -F "certificado_password=$CERT_PASSWORD"
```

### Gerar DANFE NFC-e com GET

```bash
curl -X GET "$BASE_URL/api/nfce/$CHAVE_NFCE/danfe"
```

### Gerar DANFE NFC-e com POST

```bash
curl -X POST "$BASE_URL/api/nfce/$CHAVE_NFCE/danfe" \
  -H "Content-Type: application/json" \
  -d '{
    "xml": "<nfeProc>...</nfeProc>"
  }'
```

## Observação sobre `empresa_id`

Hoje esta camada ainda não consulta banco. Então este tipo de chamada:

```bash
curl -X POST "$BASE_URL/api/nfe/criar" \
  -H "Content-Type: application/json" \
  -d '{
    "empresa_id": "uuid-ou-id"
  }'
```

retorna erro até você ligar o `empresa_id` ao seu repositório real de:

- empresa/emitente
- certificado
- configuração fiscal por ambiente
- CSC/ID CSC para NFC-e

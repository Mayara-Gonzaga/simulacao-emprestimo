
# 📊 Simulação de Empréstimos - API Laravel

API REST desenvolvida em **Laravel** para simulação de empréstimos, utilizando **arquivos JSON** para armazenar as informações de instituições, convênios e taxas.  
✅ **Sem banco de dados**.  
🔒 Todas as rotas estão protegidas pelo middleware `web`, com proteção **CSRF** aplicada manualmente para requisições `POST`.

---

## ⚙️ Requisitos do Ambiente

- **PHP**: 8.4.6  
- **Laravel**: 10.x  
- **Composer**: Última versão  
- **Sistema Operacional**: Windows (testado), Linux ou macOS  
- **Ferramentas**: Postman (para testes), VS Code (opcional)

---

## 🚀 Configuração do Projeto

1. **Clonar o repositório**

```bash
git clone https://github.com/Mayara-Gonzaga/simulacao-emprestimo.git
cd simulacao-emprestimo
```

2. **Instalar dependências**

```bash
composer install
```

3. **Configurar o ambiente**

```bash
cp .env.example .env
php artisan key:generate
```

4. **Verifique as configurações no .env**

```env
CACHE_DRIVER=file
SESSION_DRIVER=file
SESSION_COOKIE=simulador_session
```

5. **Criar arquivos JSON**

Coloque os seguintes arquivos em `storage/app`:

- **institutions.json**

```json
[
  {"chave": "PAN", "valor": "PAN"},
  {"chave": "OLE", "valor": "OLE"},
  {"chave": "BMG", "valor": "BMG"}
]
```

- **agreements.json**

```json
[
  {"chave": "inss", "valor": "INSS"},
  {"chave": "federal", "valor": "Federal"},
  {"chave": "siape", "valor": "SIAPE"}
]
```

- **rates.json**

```json
[
  {
    "institution": "BMG",
    "agreement": "inss",
    "rate": 2.05,
    "installments": [72, 60, 48, 36],
    "coefficients": [0.030597, 0.035426, 0.041466, 0.055448]
  },
  {
    "institution": "BMG",
    "agreement": "inss",
    "rate": 1.9,
    "installments": [24],
    "coefficients": [0.028651]
  },
  {
    "institution": "PAN",
    "agreement": "inss",
    "rate": 2.0,
    "installments": [60, 48],
    "coefficients": [0.0345, 0.0405]
  },
  {
    "institution": "OLE",
    "agreement": "federal",
    "rate": 2.1,
    "installments": [36],
    "coefficients": [0.0560]
  }
]
```

6. **Ajustar permissões (Windows)**

```bash
icacls storage /grant "Todos:(F)" /T
icacls bootstrap /grant "Todos:(F)" /T
```

7. **Iniciar o servidor**

```bash
php artisan serve
```

A API estará disponível em `http://127.0.0.1:8000`.

---

## 🔗 Rotas da API

Todas as rotas estão no arquivo `routes/web.php`.

- **GET /institutions**  
  Retorna a lista de instituições.

Exemplo de resposta:

```json
[
  {"chave": "PAN", "valor": "PAN"},
  {"chave": "OLE", "valor": "OLE"},
  {"chave": "BMG", "valor": "BMG"}
]
```

- **GET /agreements**  
  Retorna a lista de convênios.

Exemplo de resposta:

```json
[
  {"chave": "inss", "valor": "INSS"},
  {"chave": "federal", "valor": "Federal"},
  {"chave": "siape", "valor": "SIAPE"}
]
```

- **GET /csrf-token**  
  Retorna o token CSRF necessário para requisições POST.

Exemplo de resposta:

```json
{
  "csrf_token": "seu_token_aqui"
}
```

- **POST /simulate**  
  Simula um empréstimo com base nos parâmetros fornecidos.

Exemplo de Requisição (válida):

```bash
curl -X POST http://127.0.0.1:8000/simulate -H "Content-Type: application/json" -H "X-CSRF-TOKEN: <SEU_CSRF_TOKEN>" -d '{"valor_emprestimo": 10000, "instituicoes": ["BMG"], "convenios": ["inss"], "parcela": 72}'
```

Exemplo de Resposta:

```json
{
  "BMG": [
    {
      "taxa": 2.05,
      "parcelas": 72,
      "valor_parcela": 305.97,
      "convenio": "inss"
    }
  ]
}
```

Exemplo de Requisição (mínima):

```bash
curl -X POST http://127.0.0.1:8000/simulate -H "Content-Type: application/json" -H "X-CSRF-TOKEN: <SEU_CSRF_TOKEN>" -d '{"valor_emprestimo": 10000}'
```

Exemplo de Resposta:

```json
{
  "BMG": [
    {
      "taxa": 2.05,
      "parcelas": 72,
      "valor_parcela": 305.97,
      "convenio": "inss"
    },
    {
      "taxa": 2.05,
      "parcelas": 60,
      "valor_parcela": 354.26,
      "convenio": "inss"
    },
    {
      "taxa": 2.05,
      "parcelas": 48,
      "valor_parcela": 414.66,
      "convenio": "inss"
    },
    {
      "taxa": 2.05,
      "parcelas": 36,
      "valor_parcela": 554.48,
      "convenio": "inss"
    },
    {
      "taxa": 1.9,
      "parcelas": 24,
      "valor_parcela": 286.51,
      "convenio": "inss"
    }
  ],
  "PAN": [
    {
      "taxa": 2.0,
      "parcelas": 60,
      "valor_parcela": 345.0,
      "convenio": "inss"
    },
    {
      "taxa": 2.0,
      "parcelas": 48,
      "valor_parcela": 405.0,
      "convenio": "inss"
    }
  ],
  "OLE": [
    {
      "taxa": 2.1,
      "parcelas": 36,
      "valor_parcela": 560.0,
      "convenio": "federal"
    }
  ]
}
```

##🧪 Testes Automatizados

O projeto conta com testes automatizados de ponta a ponta, localizados em:


tests/Feature/LoanSimulationTest.php`


✅ Executar os testes
```bash
 php artisan test
```
# ou

./vendor/bin/phpunit

Os testes cobrem cenários válidos, inválidos e mínimos da rota /simulate.

---

## 🧪 Testes com Postman

Importe a coleção (https://api.postman.com/collections/44228492-8a2fe495-3a43-4be9-a3ff-afbb37541c9f?access_key=PMAT-01JS8AC5VAQX33JQXJHHXGZ131)  no Postman.

1. **Obtenha o token CSRF**: Execute a requisição **GET /csrf-token** e copie o valor de `csrf_token` retornado.

2. **Atualize as requisições POST**: Substitua `{{csrf_token}}` no header `X-CSRF-TOKEN` pelo token obtido.

3. **Execute as requisições**:
   - **GET /institutions**
   - **GET /agreements**
   - **POST /simulate** (válido, mínimo, inválido).

---

## 📝 Notas

- **CSRF**: A proteção CSRF é obrigatória para a rota **/simulate**. Sempre inclua o header `X-CSRF-TOKEN` com um token válido obtido via **GET /csrf-token**.
- **Logs**: Verifique `storage/logs/laravel.log` para depuração.
- **Git**: O projeto está versionado. Use `git add .`, `git commit`, e `git push` para atualizar o repositório.
- **Arquitetura**: A API utiliza o padrão MVC do Laravel, com controle centralizado no `SimulationController`. Os dados são armazenados em arquivos JSON (`storage/app`), atendendo ao requisito de não usar banco de dados.
- **Segurança**: A proteção CSRF garante segurança contra ataques de falsificação de requisições.

---

## 📈 Arquitetura do Projeto

- **Padrão**: MVC (Model-View-Controller) do Laravel.
- **Estrutura**:
  - **Controlador**: `app/Http/Controllers/SimulationController.php` lida com todas as rotas.
  - **Rotas**: Definidas em `routes/web.php`, usando o middleware `web` para sessões e CSRF.
  - **Dados**: Arquivos JSON em `storage/app` (`institutions.json`, `agreements.json`, `rates.json`).
  - **Validação**: Implementada no `SimulationController` com `Validator`.
  - **Segurança**: Proteção CSRF manual para requisições POST.

---

## 🔜 Escalabilidade

A API é leve e stateless (exceto pela sessão do CSRF), adequada para simulações de empréstimos. Para escalar, pode-se mover as rotas para `routes/api.php` e usar autenticação baseada em tokens (ex.: Sanctum).

Simulação de Empréstimos API

API REST desenvolvida em Laravel para simulação de empréstimos, utilizando arquivos JSON para armazenar informações de instituições, convênios e taxas. Não utiliza banco de dados. Todas as rotas estão protegidas pelo middleware web, com proteção CSRF aplicada manualmente para requisições POST.
Requisitos do Ambiente

PHP: 8.4.6
Laravel: 10.x
Composer: Última versão
Sistema Operacional: Windows (testado), Linux ou macOS
Ferramentas: Postman (para testes), VS Code (opcional)

Configuração do Projeto

Clonar o repositório:
git clone <https://github.com/Mayara-Gonzaga/simulacao-emprestimo.git>
cd simulacao-emprestimo


Instalar dependências:
composer install


Configurar o ambiente:

Copie o arquivo .env.example para .env:
copy .env.example .env


Gere a chave da aplicação:
php artisan key:generate


Confirme as configurações no .env:
CACHE_DRIVER=file
SESSION_DRIVER=file
SESSION_COOKIE=simulador_session




Criar arquivos JSON:

Coloque os arquivos institutions.json, agreements.json e rates.json em storage/app. Veja a estrutura abaixo.

Ajuste permissões (Windows):
icacls storage /grant "Todos:(F)" /T
icacls bootstrap /grant "Todos:(F)" /T




Iniciar o servidor:
php artisan serve


A API estará disponível em http://127.0.0.1:8000.



Estrutura dos Arquivos JSON
institutions.json
[
    {"chave": "PAN", "valor": "PAN"},
    {"chave": "OLE", "valor": "OLE"},
    {"chave": "BMG", "valor": "BMG"}
]

agreements.json
[
    {"chave": "inss", "valor": "INSS"},
    {"chave": "federal", "valor": "Federal"},
    {"chave": "siape", "valor": "SIAPE"}
]

rates.json
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

Rotas da API
As rotas estão definidas em routes/web.php e usam o middleware web. Requisições POST requerem um token CSRF no header X-CSRF-TOKEN, obtido via GET /csrf-token.
GET /institutions

Descrição: Retorna a lista de instituições.

Exemplo de Requisição:
curl http://127.0.0.1:8000/institutions


Exemplo de Resposta:
[
    {"chave": "PAN", "valor": "PAN"},
    {"chave": "OLE", "valor": "OLE"},
    {"chave": "BMG", "valor": "BMG"}
]



GET /agreements

Descrição: Retorna a lista de convênios.

Exemplo de Requisição:
curl http://127.0.0.1:8000/agreements


Exemplo de Resposta:
[
    {"chave": "inss", "valor": "INSS"},
    {"chave": "federal", "valor": "Federal"},
    {"chave": "siape", "valor": "SIAPE"}
]



POST /simulate

Descrição: Simula um empréstimo com base nos parâmetros fornecidos.

Parâmetros:

valor_emprestimo (float, obrigatório): Valor do empréstimo.
instituicoes (array, opcional): Lista de instituições.
convenios (array, opcional): Lista de convênios.
parcela (integer, opcional): Número de parcelas.


Exemplo de Requisição (válida):
curl -X POST http://127.0.0.1:8000/simulate \
-H "Content-Type: application/json" \
-H "X-CSRF-TOKEN: <SEU_CSRF_TOKEN>" \
-d '{"valor_emprestimo": 10000, "instituicoes": ["BMG"], "convenios": ["inss"], "parcela": 72}'


Exemplo de Resposta:
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


Exemplo de Requisição (mínima):
curl -X POST http://127.0.0.1:8000/simulate \
-H "Content-Type: application/json" \
-H "X-CSRF-TOKEN: <SEU_CSRF_TOKEN>" \
-d '{"valor_emprestimo": 10000}'


Exemplo de Resposta:
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


Exemplo de Requisição (inválida):
curl -X POST http://127.0.0.1:8000/simulate \
-H "Content-Type: application/json" \
-H "X-CSRF-TOKEN: <SEU_CSRF_TOKEN>" \
-d '{"valor_emprestimo": "", "parcela": -1}'


Exemplo de Resposta (erro 422):
{
    "errors": {
        "valor_emprestimo": [
            "The valor emprestimo field is required.",
            "The valor emprestimo field must be a number."
        ],
        "parcela": [
            "The parcela field must be at least 1."
        ]
    }
}



GET /csrf-token

Descrição: Retorna o token CSRF necessário para requisições POST.

Exemplo de Requisição:
curl http://127.0.0.1:8000/csrf-token


Exemplo de Resposta:
{
    "csrf_token": "<SEU_CSRF_TOKEN>"
}



Testes com Postman

Importe a coleção SimulacaoEmprestimo.postman_collection.json no Postman.
Obtenha o token CSRF:
Execute a requisição GET /csrf-token.
Copie o valor de csrf_token retornado.


Atualize as requisições POST:
Substitua {{csrf_token}} no header X-CSRF-TOKEN pelo token obtido.


Execute as requisições:
GET /institutions
GET /agreements
POST /simulate (válido, mínimo, inválido).



Notas

CSRF: A proteção CSRF é obrigatória para a rota /simulate. Sempre inclua o header X-CSRF-TOKEN com um token válido obtido via GET /csrf-token.
Logs: Verifique storage/logs/laravel.log para depuração.
Git: O projeto está versionado. Use git add ., git commit, e git push para atualizar o repositório.
Arquitetura: A API utiliza o padrão MVC do Laravel, com controle centralizado no SimulationController. Os dados são armazenados em arquivos JSON (storage/app), atendendo ao requisito de não usar banco de dados. A proteção CSRF garante segurança contra ataques de falsificação de requisições.

Arquitetura do Projeto

Padrão: MVC (Model-View-Controller) do Laravel.
Estrutura:
Controlador: app/Http/Controllers/SimulationController.php lida com todas as rotas.
Rotas: Definidas em routes/web.php, usando o middleware web para sessões e CSRF.
Dados: Arquivos JSON em storage/app (institutions.json, agreements.json, rates.json).
Validação: Implementada no SimulationController com Validator.
Segurança: Proteção CSRF manual para requisições POST.


Escalabilidade: A API é leve e stateless (exceto pela sessão do CSRF), adequada para simulações de empréstimos. Para escalar, pode-se mover as rotas para routes/api.php e usar autenticação baseada em tokens (ex.: Sanctum).
Interpretação da Prova: A API atende aos requisitos de fornecer endpoints para listar instituições e convênios, simular empréstimos com filtros, e calcular parcelas usando coeficientes, sem depender de banco de dados.


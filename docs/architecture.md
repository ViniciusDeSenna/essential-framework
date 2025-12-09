```TXT
essential/
│
├── src/
│   ├── Essential/                        # Namespace raiz do framework
│   │   │
│   │   ├── Core/                         # Núcleo do framework
│   │   │   ├── Application.php           # Classe principal do framework
│   │   │   ├── ServiceProvider.php       # Sistema de service providers
│   │   │   ├── Container/
│   │   │   │   ├── ContainerInterface.php
│   │   │   │   ├── Container.php
│   │   │   │   └── ContainerException.php
│   │   │   ├── Config/
│   │   │   │   ├── ConfigInterface.php
│   │   │   │   ├── Config.php
│   │   │   │   └── Repository.php        # Repositório de configurações
│   │   │   └── Support/
│   │   │       ├── Env.php
│   │   │       ├── Arr.php               # Helpers para arrays
│   │   │       ├── Str.php               # Helpers para strings
│   │   │       └── Path.php
│   │   │
│   │   ├── Http/                         # Camada HTTP
│   │   │   ├── Kernel.php                # HTTP Kernel
│   │   │   ├── Router/
│   │   │   │   ├── RouterInterface.php
│   │   │   │   ├── Route.php
│   │   │   │   ├── RouteCollection.php
│   │   │   │   └── Adapters/
│   │   │   │       └── SlimRouterAdapter.php
│   │   │   ├── Request/
│   │   │   │   ├── RequestInterface.php
│   │   │   │   └── Request.php
│   │   │   ├── Response/
│   │   │   │   ├── ResponseInterface.php
│   │   │   │   ├── Response.php
│   │   │   │   └── JsonResponse.php
│   │   │   ├── Middleware/
│   │   │   │   ├── MiddlewareInterface.php
│   │   │   │   ├── Pipeline.php          # Pipeline de middlewares
│   │   │   │   ├── CorsMiddleware.php
│   │   │   │   └── JsonBodyParserMiddleware.php
│   │   │   └── Controller/
│   │   │       └── Controller.php        # Base controller
│   │   │
│   │   ├── Database/                     # Camada de dados
│   │   │   ├── DatabaseManager.php       # Gerenciador de conexões
│   │   │   ├── Connection/
│   │   │   │   ├── ConnectionInterface.php
│   │   │   │   ├── ConnectionFactory.php
│   │   │   │   └── Adapters/
│   │   │   │       └── DoctrineConnectionAdapter.php
│   │   │   ├── Query/
│   │   │   │   ├── Builder.php           # Query builder (futuro)
│   │   │   │   └── Grammar.php
│   │   │   ├── Repository/
│   │   │   │   ├── RepositoryInterface.php
│   │   │   │   └── AbstractRepository.php
│   │   │   └── Migration/
│   │   │       ├── MigrationInterface.php
│   │   │       ├── Migrator.php
│   │   │       └── MigrationRepository.php
│   │   │
│   │   ├── Console/                      # CLI do framework
│   │   │   ├── Kernel.php                # Console Kernel
│   │   │   ├── Application.php           # CLI Application
│   │   │   ├── Command/
│   │   │   │   ├── CommandInterface.php
│   │   │   │   ├── Command.php
│   │   │   │   └── Commands/             # Comandos do framework
│   │   │   │       ├── ServeCommand.php  # Servidor de desenvolvimento
│   │   │   │       ├── Make/
│   │   │   │       │   ├── MakeControllerCommand.php
│   │   │   │       │   ├── MakeModelCommand.php
│   │   │   │       │   ├── MakeMigrationCommand.php
│   │   │   │       │   ├── MakeMiddlewareCommand.php
│   │   │   │       │   └── MakeProviderCommand.php
│   │   │   │       └── Migrate/
│   │   │   │           ├── MigrateCommand.php
│   │   │   │           ├── MigrateRollbackCommand.php
│   │   │   │           └── MigrateStatusCommand.php
│   │   │   ├── Input/
│   │   │   │   ├── InputInterface.php
│   │   │   │   └── ArgvInput.php
│   │   │   └── Output/
│   │   │       ├── OutputInterface.php
│   │   │       └── ConsoleOutput.php
│   │   │
│   │   ├── Logging/                      # Sistema de logs
│   │   │   ├── LoggerInterface.php
│   │   │   ├── Logger.php
│   │   │   └── Handlers/
│   │   │       ├── FileHandler.php
│   │   │       └── StreamHandler.php
│   │   │
│   │   ├── Cache/                        # Sistema de cache (futuro)
│   │   │   ├── CacheInterface.php
│   │   │   └── CacheManager.php
│   │   │
│   │   └── Exceptions/                   # Tratamento de exceções
│   │       ├── Handler.php
│   │       ├── EssentialException.php
│   │       └── HttpException.php
│   │
│   └── helpers.php                       # Funções globais helper (opcional)
│
├── stubs/                                # Templates para geração de código
│   ├── controller.stub
│   ├── model.stub
│   ├── migration.stub
│   ├── middleware.stub
│   └── provider.stub
│
├── config/                               # Configurações padrão do framework
│   └── essential.php                     # Defaults que podem ser overridden
│
├── bin/
│   └── essential                         # CLI executable (será linkado via Composer)
│
├── tests/                                # Testes do framework
│   ├── Unit/
│   │   ├── Core/
│   │   ├── Http/
│   │   ├── Database/
│   │   └── Console/
│   ├── Integration/
│   └── TestCase.php
│
├── docs/                                 # Documentação do framework
│   ├── installation.md
│   ├── routing.md
│   ├── database.md
│   ├── cli.md
│   └── architecture.md
│
├── .github/
│   └── workflows/
│       └── tests.yml                     # CI/CD para o framework
│
├── composer.json                         # Define Essential como pacote
├── phpunit.xml
├── .gitignore
├── README.md                             # Documentação principal
├── CHANGELOG.md                          # Histórico de versões
├── LICENSE                               # MIT
└── CONTRIBUTING.md                       # Guia de contribuição
```

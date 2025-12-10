<?php

declare(strict_types=1);

use Symplify\MonorepoBuilder\Config\MBConfig;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\AddTagToChangelogReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\PushNextDevReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\PushTagReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\SetCurrentMutualDependenciesReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\SetNextMutualDependenciesReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\TagVersionReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\UpdateBranchAliasReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\UpdateReplaceReleaseWorker;
use Symplify\MonorepoBuilder\ValueObject\Option;

return static function (MBConfig $mbConfig): void {
    // Diretórios que contêm os pacotes
    $mbConfig->packageDirectories([
        __DIR__ . '/packages',
    ]);

    // Diretórios para excluir da análise
    $mbConfig->packageDirectoriesExcludes([
        __DIR__ . '/packages/*/tests',
        __DIR__ . '/packages/*/vendor',
    ]);

    // Dependências que serão adicionadas ao composer.json de todos os pacotes
    $mbConfig->dataToAppend([
        'require-dev' => [
            'phpunit/phpunit' => '^10.5',
            'mockery/mockery' => '^1.6',
        ],
        'config' => [
            'sort-packages' => true,
            'optimize-autoloader' => true,
            'preferred-install' => 'dist',
        ],
        'minimum-stability' => 'dev',
        'prefer-stable' => true,
    ]);

    // Dependências que serão removidas dos pacotes individuais
    // (porque já estão no root composer.json)
    $mbConfig->dataToRemove([
        'require-dev' => [
            'symplify/monorepo-builder' => '*',
            'symplify/easy-coding-standard' => '*',
            'friendsofphp/php-cs-fixer' => '*',
        ],
    ]);

    // Branch alias padrão
    $mbConfig->defaultBranch('main');

    // Workers executados durante o release
    $mbConfig->workers([
        // 1. Atualiza a versão no branch-alias
        UpdateBranchAliasReleaseWorker::class,

        // 2. Atualiza dependências entre pacotes para a versão atual
        SetCurrentMutualDependenciesReleaseWorker::class,

        // 3. Adiciona entrada no CHANGELOG.md
        AddTagToChangelogReleaseWorker::class,

        // 4. Cria a tag da versão
        TagVersionReleaseWorker::class,

        // 5. Faz push da tag
        PushTagReleaseWorker::class,

        // 6. Atualiza dependências entre pacotes para a próxima versão dev
        SetNextMutualDependenciesReleaseWorker::class,

        // 7. Faz push da branch com a próxima versão dev
        PushNextDevReleaseWorker::class,

        // 8. Atualiza seção "replace" no root composer.json
        UpdateReplaceReleaseWorker::class,
    ]);
};

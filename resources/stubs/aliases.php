<?php

declare(strict_types=1);

namespace App;

$aliases = [
    // cycle/database
    'Cycle\\Database\\ColumnInterface' => 'Spiral\\Database\\ColumnInterface',
    'Cycle\\Database\\DatabaseInterface' => 'Spiral\\Database\\DatabaseInterface',
    'Cycle\\Database\\DatabaseProviderInterface' => 'Spiral\\Database\\DatabaseProviderInterface',
    'Cycle\\Database\\ForeignKeyInterface' => 'Spiral\\Database\\ForeignKeyInterface',
    'Cycle\\Database\\IndexInterface' => 'Spiral\\Database\\IndexInterface',
    'Cycle\\Database\\StatementInterface' => 'Spiral\\Database\\StatementInterface',
    'Cycle\\Database\\TableInterface' => 'Spiral\\Database\\TableInterface',
    'Cycle\\Database\\Database' => 'Spiral\\Database\\Database',
    'Cycle\\Database\\DatabaseManager' => 'Spiral\\Database\\DatabaseManager',
    'Cycle\\Database\\Table' => 'Spiral\\Database\\Table',
    'Cycle\\Database\\Schema\\ComparatorInterface' => 'Spiral\\Database\\Schema\\ComparatorInterface',
    'Cycle\\Database\\Schema\\ElementInterface' => 'Spiral\\Database\\Schema\\ElementInterface',
    'Cycle\\Database\\Schema\\AbstractColumn' => 'Spiral\\Database\\Schema\\AbstractColumn',
    'Cycle\\Database\\Schema\\AbstractForeignKey' => 'Spiral\\Database\\Schema\\AbstractForeignKey',
    'Cycle\\Database\\Schema\\AbstractIndex' => 'Spiral\\Database\\Schema\\AbstractIndex',
    'Cycle\\Database\\Schema\\AbstractTable' => 'Spiral\\Database\\Schema\\AbstractTable',
    'Cycle\\Database\\Schema\\Comparator' => 'Spiral\\Database\\Schema\\Comparator',
    'Cycle\\Database\\Schema\\Reflector' => 'Spiral\\Database\\Schema\\Reflector',
    'Cycle\\Database\\Schema\\State' => 'Spiral\\Database\\Schema\\State',
    'Cycle\\Database\\Schema\\Traits\\ElementTrait' => 'Spiral\\Database\\Schema\\Traits\\ElementTrait',
    'Cycle\\Database\\Query\\BuilderInterface' => 'Spiral\\Database\\Query\\BuilderInterface',
    'Cycle\\Database\\Query\\QueryInterface' => 'Spiral\\Database\\Query\\QueryInterface',
    'Cycle\\Database\\Query\\ActiveQuery' => 'Spiral\\Database\\Query\\ActiveQuery',
    'Cycle\\Database\\Query\\DeleteQuery' => 'Spiral\\Database\\Query\\DeleteQuery',
    'Cycle\\Database\\Query\\InsertQuery' => 'Spiral\\Database\\Query\\InsertQuery',
    'Cycle\\Database\\Query\\Interpolator' => 'Spiral\\Database\\Query\\Interpolator',
    'Cycle\\Database\\Query\\QueryBuilder' => 'Spiral\\Database\\Query\\QueryBuilder',
    'Cycle\\Database\\Query\\QueryParameters' => 'Spiral\\Database\\Query\\QueryParameters',
    'Cycle\\Database\\Query\\SelectQuery' => 'Spiral\\Database\\Query\\SelectQuery',
    'Cycle\\Database\\Query\\UpdateQuery' => 'Spiral\\Database\\Query\\UpdateQuery',
    'Cycle\\Database\\Query\\Traits\\HavingTrait' => 'Spiral\\Database\\Query\\Traits\\HavingTrait',
    'Cycle\\Database\\Query\\Traits\\JoinTrait' => 'Spiral\\Database\\Query\\Traits\\JoinTrait',
    'Cycle\\Database\\Query\\Traits\\TokenTrait' => 'Spiral\\Database\\Query\\Traits\\TokenTrait',
    'Cycle\\Database\\Query\\Traits\\WhereTrait' => 'Spiral\\Database\\Query\\Traits\\WhereTrait',
    'Cycle\\Database\\Injection\\FragmentInterface' => 'Spiral\\Database\\Injection\\FragmentInterface',
    'Cycle\\Database\\Injection\\ParameterInterface' => 'Spiral\\Database\\Injection\\ParameterInterface',
    'Cycle\\Database\\Injection\\ValueInterface' => 'Spiral\\Database\\Injection\\ValueInterface',
    'Cycle\\Database\\Injection\\Expression' => 'Spiral\\Database\\Injection\\Expression',
    'Cycle\\Database\\Injection\\Fragment' => 'Spiral\\Database\\Injection\\Fragment',
    'Cycle\\Database\\Injection\\Parameter' => 'Spiral\\Database\\Injection\\Parameter',
    'Cycle\\Database\\Exception\\StatementExceptionInterface' => 'Spiral\\Database\\Exception\\StatementExceptionInterface',
    'Cycle\\Database\\Exception\\BuilderException' => 'Spiral\\Database\\Exception\\BuilderException',
    'Cycle\\Database\\Exception\\CompilerException' => 'Spiral\\Database\\Exception\\CompilerException',
    'Cycle\\Database\\Exception\\ConfigException' => 'Spiral\\Database\\Exception\\ConfigException',
    'Cycle\\Database\\Exception\\DatabaseException' => 'Spiral\\Database\\Exception\\DatabaseException',
    'Cycle\\Database\\Exception\\DBALException' => 'Spiral\\Database\\Exception\\DBALException',
    'Cycle\\Database\\Exception\\DefaultValueException' => 'Spiral\\Database\\Exception\\DefaultValueException',
    'Cycle\\Database\\Exception\\DriverException' => 'Spiral\\Database\\Exception\\DriverException',
    'Cycle\\Database\\Exception\\HandlerException' => 'Spiral\\Database\\Exception\\HandlerException',
    'Cycle\\Database\\Exception\\InterpolatorException' => 'Spiral\\Database\\Exception\\InterpolatorException',
    'Cycle\\Database\\Exception\\SchemaException' => 'Spiral\\Database\\Exception\\SchemaException',
    'Cycle\\Database\\Exception\\StatementException' => 'Spiral\\Database\\Exception\\StatementException',
    'Spiral\\Database\\Exception\\StatementException\\ConnectionException' => 'Spiral\\Database\\Exception\\StatementException\\ConnectionException',
    'Spiral\\Database\\Exception\\StatementException\\ConstrainException' => 'Spiral\\Database\\Exception\\StatementException\\ConstrainException',
    'Cycle\\Database\\Driver\\CachingCompilerInterface' => 'Spiral\\Database\\Driver\\CachingCompilerInterface',
    'Cycle\\Database\\Driver\\CompilerInterface' => 'Spiral\\Database\\Driver\\CompilerInterface',
    'Cycle\\Database\\Driver\\DriverInterface' => 'Spiral\\Database\\Driver\\DriverInterface',
    'Cycle\\Database\\Driver\\HandlerInterface' => 'Spiral\\Database\\Driver\\HandlerInterface',
    'Cycle\\Database\\Driver\\Compiler' => 'Spiral\\Database\\Driver\\Compiler',
    'Cycle\\Database\\Driver\\Driver' => 'Spiral\\Database\\Driver\\Driver',
    'Cycle\\Database\\Driver\\Handler' => 'Spiral\\Database\\Driver\\Handler',
    'Cycle\\Database\\Driver\\CompilerCache' => 'Spiral\\Database\\Driver\\CompilerCache',
    'Cycle\\Database\\Driver\\Quoter' => 'Spiral\\Database\\Driver\\Quoter',
    'Cycle\\Database\\Driver\\ReadonlyHandler' => 'Spiral\\Database\\Driver\\ReadonlyHandler',
    'Cycle\\Database\\Driver\\Statement' => 'Spiral\\Database\\Driver\\Statement',
    'Cycle\\Database\\Driver\\MySQL\\MySQLCompiler' => 'Spiral\\Database\\Driver\\MySQL\\MySQLCompiler',
    'Cycle\\Database\\Driver\\MySQL\\MySQLDriver' => 'Spiral\\Database\\Driver\\MySQL\\MySQLDriver',
    'Cycle\\Database\\Driver\\MySQL\\MySQLHandler' => 'Spiral\\Database\\Driver\\MySQL\\MySQLHandler',
    'Cycle\\Database\\Driver\\MySQL\\Exception\\MySQLException' => 'Spiral\\Database\\Driver\\MySQL\\Exception\\MySQLException',
    'Cycle\\Database\\Driver\\MySQL\\Schema\\MySQLColumn' => 'Spiral\\Database\\Driver\\MySQL\\Schema\\MySQLColumn',
    'Cycle\\Database\\Driver\\MySQL\\Schema\\MySQLForeignKey' => 'Spiral\\Database\\Driver\\MySQL\\Schema\\MySQLForeignKey',
    'Cycle\\Database\\Driver\\MySQL\\Schema\\MySQLIndex' => 'Spiral\\Database\\Driver\\MySQL\\Schema\\MySQLIndex',
    'Cycle\\Database\\Driver\\MySQL\\Schema\\MySQLTable' => 'Spiral\\Database\\Driver\\MySQL\\Schema\\MySQLTable',
    'Cycle\\Database\\Driver\\Postgres\\PostgresCompiler' => 'Spiral\\Database\\Driver\\Postgres\\PostgresCompiler',
    'Cycle\\Database\\Driver\\Postgres\\PostgresDriver' => 'Spiral\\Database\\Driver\\Postgres\\PostgresDriver',
    'Cycle\\Database\\Driver\\Postgres\\PostgresHandler' => 'Spiral\\Database\\Driver\\Postgres\\PostgresHandler',
    'Cycle\\Database\\Driver\\Postgres\\Query\\PostgresInsertQuery' => 'Spiral\\Database\\Driver\\Postgres\\Query\\PostgresInsertQuery',
    'Cycle\\Database\\Driver\\Postgres\\Query\\PostgresSelectQuery' => 'Spiral\\Database\\Driver\\Postgres\\Query\\PostgresSelectQuery',
    'Cycle\\Database\\Driver\\Postgres\\Schema\\PostgresColumn' => 'Spiral\\Database\\Driver\\Postgres\\Schema\\PostgresColumn',
    'Cycle\\Database\\Driver\\Postgres\\Schema\\PostgresForeignKey' => 'Spiral\\Database\\Driver\\Postgres\\Schema\\PostgresForeignKey',
    'Cycle\\Database\\Driver\\Postgres\\Schema\\PostgresIndex' => 'Spiral\\Database\\Driver\\Postgres\\Schema\\PostgresIndex',
    'Cycle\\Database\\Driver\\Postgres\\Schema\\PostgresTable' => 'Spiral\\Database\\Driver\\Postgres\\Schema\\PostgresTable',
    'Cycle\\Database\\Driver\\SQLite\\SQLiteCompiler' => 'Spiral\\Database\\Driver\\SQLite\\SQLiteCompiler',
    'Cycle\\Database\\Driver\\SQLite\\SQLiteDriver' => 'Spiral\\Database\\Driver\\SQLite\\SQLiteDriver',
    'Cycle\\Database\\Driver\\SQLite\\SQLiteHandler' => 'Spiral\\Database\\Driver\\SQLite\\SQLiteHandler',
    'Cycle\\Database\\Driver\\SQLite\\Schema\\SQLiteColumn' => 'Spiral\\Database\\Driver\\SQLite\\Schema\\SQLiteColumn',
    'Cycle\\Database\\Driver\\SQLite\\Schema\\SQLiteForeignKey' => 'Spiral\\Database\\Driver\\SQLite\\Schema\\SQLiteForeignKey',
    'Cycle\\Database\\Driver\\SQLite\\Schema\\SQLiteIndex' => 'Spiral\\Database\\Driver\\SQLite\\Schema\\SQLiteIndex',
    'Cycle\\Database\\Driver\\SQLite\\Schema\\SQLiteTable' => 'Spiral\\Database\\Driver\\SQLite\\Schema\\SQLiteTable',
    'Cycle\\Database\\Driver\\SQLServer\\SQLServerCompiler' => 'Spiral\\Database\\Driver\\SQLServer\\SQLServerCompiler',
    'Cycle\\Database\\Driver\\SQLServer\\SQLServerDriver' => 'Spiral\\Database\\Driver\\SQLServer\\SQLServerDriver',
    'Cycle\\Database\\Driver\\SQLServer\\SQLServerHandler' => 'Spiral\\Database\\Driver\\SQLServer\\SQLServerHandler',
    'Cycle\\Database\\Driver\\SQLServer\\Schema\\SQLServerColumn' => 'Spiral\\Database\\Driver\\SQLServer\\Schema\\SQLServerColumn',
    'Cycle\\Database\\Driver\\SQLServer\\Schema\\SQLServerForeignKey' => 'Spiral\\Database\\Driver\\SQLServer\\Schema\\SQlServerForeignKey',
    'Cycle\\Database\\Driver\\SQLServer\\Schema\\SQLServerIndex' => 'Spiral\\Database\\Driver\\SQLServer\\Schema\\SQLServerIndex',
    'Cycle\\Database\\Driver\\SQLServer\\Schema\\SQLServerTable' => 'Spiral\\Database\\Driver\\SQLServer\\Schema\\SQLServerTable',
    'Cycle\\Database\\Config\\DatabaseConfig' => 'Spiral\\Database\\Config\\DatabaseConfig',
    'Cycle\\Database\\Config\\DatabasePartial' => 'Spiral\\Database\\Config\\DatabasePartial',

    // cycle/migrations
    'Cycle\\Migrations\\CapsuleInterface' => 'Spiral\\Migrations\\CapsuleInterface',
    'Cycle\\Migrations\\MigrationInterface' => 'Spiral\\Migrations\\MigrationInterface',
    'Cycle\\Migrations\\MigratorInterface' => 'Spiral\\Migrations\\MigratorInterface',
    'Cycle\\Migrations\\OperationInterface' => 'Spiral\\Migrations\\OperationInterface',
    'Cycle\\Migrations\\RepositoryInterface' => 'Spiral\\Migrations\\RepositoryInterface',
    'Cycle\\Migrations\\Capsule' => 'Spiral\\Migrations\\Capsule',
    'Cycle\\Migrations\\FileRepository' => 'Spiral\\Migrations\\FileRepository',
    'Cycle\\Migrations\\Migrator' => 'Spiral\\Migrations\\Migrator',
    'Cycle\\Migrations\\State' => 'Spiral\\Migrations\\State',
    'Cycle\\Migrations\\TableBlueprint' => 'Spiral\\Migrations\\TableBlueprint',
    'Cycle\\Migrations\\Migration' => 'Spiral\\Migrations\\Migration',
    'Cycle\\Migrations\\Operation\\AbstractOperation' => 'Spiral\\Migrations\\Operation\\AbstractOperation',
    'Cycle\\Migrations\\Operation\\Column\\Add' => 'Spiral\\Migrations\\Operation\\Column\\Add',
    'Cycle\\Migrations\\Operation\\Column\\Alter' => 'Spiral\\Migrations\\Operation\\Column\\Alter',
    'Cycle\\Migrations\\Operation\\Column\\Drop' => 'Spiral\\Migrations\\Operation\\Column\\Drop',
    'Cycle\\Migrations\\Operation\\Column\\Rename' => 'Spiral\\Migrations\\Operation\\Column\\Rename',
    'Cycle\\Migrations\\Operation\\Column\\Column' => 'Spiral\\Migrations\\Operation\\Column\\Column',
    'Cycle\\Migrations\\Operation\\ForeignKey\\Add' => 'Spiral\\Migrations\\Operation\\ForeignKey\\Add',
    'Cycle\\Migrations\\Operation\\ForeignKey\\Alter' => 'Spiral\\Migrations\\Operation\\ForeignKey\\Alter',
    'Cycle\\Migrations\\Operation\\ForeignKey\\Drop' => 'Spiral\\Migrations\\Operation\\ForeignKey\\Drop',
    'Cycle\\Migrations\\Operation\\ForeignKey\\ForeignKey' => 'Spiral\\Migrations\\Operation\\ForeignKey\\ForeignKey',
    'Cycle\\Migrations\\Operation\\Index\\Add' => 'Spiral\\Migrations\\Operation\\Index\\Add',
    'Cycle\\Migrations\\Operation\\Index\\Alter' => 'Spiral\\Migrations\\Operation\\Index\\Alter',
    'Cycle\\Migrations\\Operation\\Index\\Drop' => 'Spiral\\Migrations\\Operation\\Index\\Drop',
    'Cycle\\Migrations\\Operation\\Index\\Index' => 'Spiral\\Migrations\\Operation\\Index\\Index',
    'Cycle\\Migrations\\Operation\\Table\\Create' => 'Spiral\\Migrations\\Operation\\Table\\Create',
    'Cycle\\Migrations\\Operation\\Table\\Drop' => 'Spiral\\Migrations\\Operation\\Table\\Drop',
    'Cycle\\Migrations\\Operation\\Table\\PrimaryKeys' => 'Spiral\\Migrations\\Operation\\Table\\PrimaryKeys',
    'Cycle\\Migrations\\Operation\\Table\\Rename' => 'Spiral\\Migrations\\Operation\\Table\\Rename',
    'Cycle\\Migrations\\Operation\\Table\\Update' => 'Spiral\\Migrations\\Operation\\Table\\Update',
    'Cycle\\Migrations\\Operation\\Traits\\OptionsTrait' => 'Spiral\\Migrations\\Operation\\Traits\\OptionsTrait',
    'Cycle\\Migrations\\Migrator\\MigrationsTable' => 'Spiral\\Migrations\\Migrator\\MigrationsTable',
    'Cycle\\Migrations\\Migration\\DefinitionInterface' => 'Spiral\\Migrations\\Migration\\DefinitionInterface',
    'Cycle\\Migrations\\Migration\\ProvidesSyncStateInterface' => 'Spiral\\Migrations\\Migration\\ProvidesSyncStateInterface',
    'Cycle\\Migrations\\Migration\\State' => 'Spiral\\Migrations\\Migration\\State',
    'Cycle\\Migrations\\Migration\\Status' => 'Spiral\\Migrations\\Migration\\Status',
    'Cycle\\Migrations\\Exception\\BlueprintException' => 'Spiral\\Migrations\\Exception\\BlueprintException',
    'Cycle\\Migrations\\Exception\\CapsuleException' => 'Spiral\\Migrations\\Exception\\CapsuleException',
    'Cycle\\Migrations\\Exception\\ContextException' => 'Spiral\\Migrations\\Exception\\ContextException',
    'Cycle\\Migrations\\Exception\\MigrationException' => 'Spiral\\Migrations\\Exception\\MigrationException',
    'Cycle\\Migrations\\Exception\\OperationException' => 'Spiral\\Migrations\\Exception\\OperationException',
    'Cycle\\Migrations\\Exception\\RepositoryException' => 'Spiral\\Migrations\\Exception\\RepositoryException',
    'Cycle\\Migrations\\Exception\\Operation\\ColumnException' => 'Spiral\\Migrations\\Operation\\Exception\\Operation\\ColumnException',
    'Cycle\\Migrations\\Exception\\Operation\\ForeignKeyException' => 'Spiral\\Migrations\\Operation\\Exception\\Operation\\ForeignKeyException',
    'Cycle\\Migrations\\Exception\\Operation\\IndexException' => 'Spiral\\Migrations\\Operation\\Exception\\Operation\\IndexException',
    'Cycle\\Migrations\\Exception\\Operation\\TableException' => 'Spiral\\Migrations\\Operation\\Exception\\Operation\\TableException',
    'Cycle\\Migrations\\Config\\MigrationConfig' => 'Spiral\\Migrations\\Config\\MigrationConfig',
    'Cycle\\Migrations\\Atomizer\\RendererInterface' => 'Spiral\\Migrations\\Atomizer\\RendererInterface',
    'Cycle\\Migrations\\Atomizer\\Atomizer' => 'Spiral\\Migrations\\Atomizer\\Atomizer',
    'Cycle\\Migrations\\Atomizer\\Renderer' => 'Spiral\\Migrations\\Atomizer\\Renderer',

    // cycle/schema-migrations-generator
    'Cycle\\Schema\\Generator\\Migrations\\GenerateMigrations' => 'Cycle\\Migrations\\GenerateMigrations',
    'Cycle\\Schema\\Generator\\Migrations\\MigrationImage' => 'Cycle\\Migrations\\MigrationImage',
];

function triggerClassAutoload(string $class): bool
{
    if (\class_exists($class)) {
        return true;
    }

    if (\interface_exists($class)) {
        return true;
    }

    if (\trait_exists($class)) {
        return true;
    }

    if (\enum_exists($class)) {
        return true;
    }

    return false;
}

foreach ($aliases as $class => $alias) {
    if (!triggerClassAutoload($class)) {
        continue;
    }

    if (triggerClassAutoload($alias)) {
        continue;
    }

    \class_alias($class, $alias, false);
}

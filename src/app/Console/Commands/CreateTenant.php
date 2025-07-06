<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreateTenant extends Command
{
    protected $signature    = 'tenant:create {name} {--slug=}';
    protected $description  = 'Cria um novo tenant com o banco de dados e roda as migrations';

    public function handle()
    {
        $name = $this->argument('name');
        $slug = $this->option('slug') ?? Str::slug($name);
        $database = 'tenant_' . $slug;

        // Cria o banco de dados (precisa de permissões via root)
        DB::statement("CREATE DATABASE `$database`");

        // Cria o registro do tenant
        $tenant = Tenant::create([
            'name'     => $name,
            'slug'     => $slug,
            'database' => $database,
        ]);

        // Define dinamicamente o banco na conexão tenant
        config()->set('database.connections.tenant.database', $database);

        // Marca esse tenant como atual
        $tenant->makeCurrent();

        // Roda as migrations no banco do tenant
        Artisan::call('migrate', [
            '--database'    => 'tenant',
            '--force'       => true
        ]);

        $this->info("Tenant '{$tenant->slug}' criado com sucesso com banco '$database'.");
    }
}

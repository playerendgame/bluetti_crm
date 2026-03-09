<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Admin;
use App\Models\ApiToken;
use Illuminate\Support\Str;

class GenerateApiToken extends Command
{
    protected $signature = 'api:generate-token {email} {--name=}';

    protected $description = 'Generate API token for an admin';

    public function handle()
    {
        $email = $this->argument('email');
        $name = $this->option('name') ?: 'SSO Integration Token';
        
        $admin = Admin::where('email', $email)->first();

        if (!$admin) {
            $this->error('Admin not found');
            return;
        }

        $token = Str::random(80);
        
        ApiToken::create([
            'admin_id' => $admin->id,
            'token' => hash('sha256', $token),
            'name' => $name,
            'expires_at' => null
        ]);

        $this->info('API Token generated for ' . $admin->email . ':');
        $this->line($token);
        $this->info('Token name: ' . $name);
    }
}
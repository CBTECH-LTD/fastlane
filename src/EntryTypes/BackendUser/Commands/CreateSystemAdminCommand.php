<?php

namespace CbtechLtd\Fastlane\EntryTypes\BackendUser\Commands;

use CbtechLtd\Fastlane\EntryTypes\BackendUser\BackendUserEntryType;
use CbtechLtd\Fastlane\EntryTypes\BackendUser\Model\User;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class CreateSystemAdminCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fastlane:system-admin:make';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new backend user';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (! $name = $this->ask('Name', null)) {
            $this->error('No name provided.');
            return false;
        }

        if (! $email = $this->ask('Email', null)) {
            $this->error('No email provided.');
            return false;
        }

        if (! $password = $this->ask('Password', Str::random(16))) {
            $this->error('No password provided.');
            return false;
        }

        $user = User::create([
            'name'     => $name,
            'email'    => $email,
            'password' => $password,
        ]);

        $user->assignRole(BackendUserEntryType::ROLE_SYSTEM_ADMIN);

        $this->info('User created with ID ' . $user->getKey());
    }
}

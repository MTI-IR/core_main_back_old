<?php

namespace Deployer;

require 'recipe/laravel.php';

// Config

set('repository', 'git@github.com:MTI-IR/core_main_back.git');

add('shared_files', []);
add('shared_dirs', []);
add('writable_dirs', []);

// Hosts
// doc: https://github.com/deployphp/deployer/blob/master/docs/UPGRADE.md
host('mtii.ir')
    ->stage('production')
    ->user('root')
    ->set('labels', ['stage' => 'prod'])
    ->set('remote_user', 'deployer')
    ->set('deploy_path', 'var/www/html');

// Hooks

after('deploy:failed', 'deploy:unlock');

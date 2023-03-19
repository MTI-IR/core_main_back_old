<?php

namespace Deployer;

require 'recipe/laravel.php';

// Config

set('repository', 'git@github.com:MTI-IR/core_main_back.git');

add('shared_files', []);
add('shared_dirs', []);
add('writable_dirs', []);

// Hosts

host('')
    ->set('labels', ['server_type' => 'app'])
    ->set('remote_user', 'deployer')
    ->set('deploy_path', '~/mti_main_back');

// Hooks

after('deploy:failed', 'deploy:unlock');

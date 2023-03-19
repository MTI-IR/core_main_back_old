<?php

namespace Deployer;

require 'recipe/laravel.php';
require 'contrib/php-fpm.php';
require 'contrib/npm.php';

set('application', 'mti_main_core');
set('repository', 'git@github.com:MTI-IR/core_main_back.git');
set('php_fpm_version', '8.0');

host('prod')
    ->set('remote_user', 'root')
    ->set('hostname', 'mti_main_core.ir')
    ->set('deploy_path', '/var/www/{{hostname}}');

task('deploy', [
    'deploy:prepare',
    'deploy:vendors',
    'artisan:storage:link',
    'artisan:view:cache',
    'artisan:config:cache',
    'artisan:migrate',
    'npm:install',
    'npm:run:prod',
    'deploy:publish',
    'php-fpm:reload',
]);

task('npm:run:prod', function () {
    cd('{{release_or_current_path}}');
    run('npm run prod');
});

after('deploy:failed', 'deploy:unlock');

<?php

/**
 * Permission is hereby granted, free of charge, to any person obtaining a
 * copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS
 * IN THE SOFTWARE.
 *
 * @category   deployer
 * @author     Michael Lämmlein <laemmi@spacerabbit.de>
 * @copyright  ©2020 laemmi
 * @license    http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version    1.0.0
 * @since      23.11.20
 */

declare(strict_types=1);

namespace Deployer;

import('recipe/common.php');
import('contrib/rsync.php');
import('contrib/cachetool.php');

add('recipes', ['contao4']);

// Settings common
set('keep_releases', 3);

set('shared_dirs', [
    'config',
    'assets/images',
    'files/shared',
    'web/share',
    'system/config',
    'var/backups',
    'var/logs',
]);

set('shared_files', [
    '.env.local',
//    'config/parameters.yml',
]);

set('writable_dirs', [
    'var',
    'var/cache',
    'var/log',
    'var/sessions',
]);

// Settings Contao
set('bin/console', '{{bin/php}} {{release_or_current_path}}/vendor/bin/contao-console');

set('console_options', function () {
    return '--no-interaction';
});

// Settings rsync
set('rsync_src', getcwd());
add('rsync', [
    'include' => [
        '/assets/',
        '/files/',
        '/system/',
        '/templates/',
        '/vendor/',
        '/var/',
        '/web/',
        '/composer.json',
        '/composer.lock',
    ],
    'exclude' => [
        '/*',
        '.gitkeep',
        '.gitignore',
        '.DS_Store',
        'LICENSE',
        'README.md',
    ],
    'flags' => 'rlz'
]);

// Tasks
task('contao:cache:clear', function () {
    run('cd {{release_or_current_path}} && {{bin/console}} cache:clear {{console_options}}');
});

task('contao:migrate', function () {
    run('cd {{release_or_current_path}} && {{bin/console}} contao:migrate {{console_options}}');
});

task('contao:symlinks', function () {
    run('cd {{release_or_current_path}} && {{bin/console}} contao:symlinks {{console_options}}');
});

desc('Deploy the project');
task('deploy', [
    'deploy:prepare',
    'contao:cache:clear',
    'contao:migrate',
    'contao:symlinks',
    'deploy:publish',
]);

task('deploy:vendors')->disable();
task('deploy:update_code')->disable();

after('deploy:update_code', 'rsync');
after('deploy:symlink', 'cachetool:clear:opcache');
after('deploy:failed', 'deploy:unlock');

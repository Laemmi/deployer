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

require 'recipe/contao4.php';
require 'recipe/rsync.php';
require 'recipe/cachetool.php';

// Settings
set('install_dir', 'www');
set('public_path', '{{release_path}}/{{install_dir}}/web');
set('bin/contao-console', 'vendor/bin/contao-console');
set('bin/cachetool', 'vendor/bin/cachetool');
set('rsync_src', getcwd());
add('rsync', [
    'include' => [
        '/config/',
        '/vendor/',
        '/{{install_dir}}/',
        '/{{install_dir}}/assets/',
        '/{{install_dir}}/files/',
        '/{{install_dir}}/system/',
        '/{{install_dir}}/templates/',
        '/{{install_dir}}/vendor/',
        '/{{install_dir}}/web/'
    ],
    'exclude' => [
        '/*',
        '.gitkeep',
        '.gitignore',
        '.DS_Store',
        'LICENSE',
        'README.md',
        '/{{install_dir}}/*'
    ],
    'flags' => 'rlz'
]);

// Prepare shared / writable
(function () {
    $callback = function ($val) {
        return '{{install_dir}}/' . $val;
    };
    set('shared_dirs', array_map($callback, get('shared_dirs')));
    set('shared_files', array_map($callback, get('shared_files')));
    set('writable_dirs', array_map($callback, get('writable_dirs')));
})();

// User tasks
task('deploy:update_code', [
    'rsync'
]);

task(
    'build',
    function () {
        set('release_path', getcwd() . '/{{install_dir}}');
        invoke('deploy:vendors');
    }
)->desc('Build task local')->local();

task(
    'contao:console:tasks',
    function () {
        $release_path = get('release_path');
        set('release_path', $release_path . '/{{install_dir}}');
        invoke('contao:console:cache:clear');
        invoke('contao:console:migrate');
        invoke('contao:console:symlinks');
        set('release_path', $release_path);
    }
)->desc('Contao console tasks');

task('release', [
    'deploy:info',
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'deploy:update_code',
    'deploy:shared',
    'deploy:writable',
    'contao:console:tasks',
    'deploy:symlink',
    'cachetool:clear:opcache',
    'deploy:unlock',
])->desc('Release task');

task('deploy', [
    'build',
    'release',
    'cleanup',
    'success'
])->desc('Deploy task');

after('deploy:failed', 'deploy:unlock');

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

namespace Deployer;

// Settings
set('bin/contao-console', '{{release_path}}/bin/contao-console');

set('shared_dirs', [
    'config',
    'files/shared',
    'var/logs',
    'web/share',
]);

set('shared_files', [
    'system/config/localconfig.php',
]);

set('writable_dirs', [
    'var'
]);

// Tasks
task('contao:console:cache:clear', '
    {{bin/php}} {{bin/contao-console}} cache:clear --no-interaction --env=prod
')->desc('Execute contao console cache:clear');

task('contao:console:migrate', '
    {{bin/php}} {{bin/contao-console}} contao:migrate --no-interaction --env=prod
')->desc('Execute contao console migrate');

task('contao:console:symlinks', '
    {{bin/php}} {{bin/contao-console}} contao:symlinks --no-interaction --env=prod
')->desc('Execute contao console symlinks');

task('deploy', [
    'deploy:info',
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'deploy:update_code',
    'deploy:shared',
    'deploy:vendors',
    'deploy:writable',
    'contao:console:cache:clear',
    'contao:console:migrate',
    'contao:console:symlinks',
    'deploy:symlink',
    'deploy:unlock',
    'cleanup',
])->desc('Deploy your Contao project');
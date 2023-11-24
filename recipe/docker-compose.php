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
 * @copyright  ©2022 laemmi
 * @license    http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version    1.0.0
 * @since      21.01.22
 */

declare(strict_types=1);

namespace Deployer;

add('recipes', ['laemmis-docker-compose']);

// Settings
set('docker-compose.yml', 'docker-compose.yml');
set('container_name', 'app');
set('CI_REGISTRY_PASSWORD', getenv('CI_REGISTRY_PASSWORD'));
set('CI_REGISTRY_USER', getenv('CI_REGISTRY_USER'));
set('CI_REGISTRY', getenv('CI_REGISTRY'));

task('deploy:docker:login', function () {
    run('echo {{CI_REGISTRY_PASSWORD}} | docker login -u {{CI_REGISTRY_USER}} --password-stdin {{CI_REGISTRY}}');
});

task('deploy:docker-compose:pull', function () {
    cd('{{deploy_path}}');
    run('docker-compose -f {{docker-compose.yml}} pull {{container_name}}');
});

task('deploy:docker-compose:up', function () {
    cd('{{deploy_path}}');
    run('docker-compose -f {{docker-compose.yml}} up -d');
});

task('deploy:docker-compose:restart', function () {
    cd('{{deploy_path}}');
    run('docker-compose -f {{docker-compose.yml}} restart');
});

task('deploy:docker-compose', [
    'deploy:docker:login',
    'deploy:docker-compose:pull',
    'deploy:docker-compose:up',
//    'deploy:docker-compose:restart',
]);

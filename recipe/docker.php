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

// Settings
set('docker-compose.yml', 'docker-compose.yml');
set('CI_REGISTRY_PASSWORD', getenv('CI_REGISTRY_PASSWORD'));
set('CI_REGISTRY_USER', getenv('CI_REGISTRY_USER'));
set('CI_REGISTRY', getenv('CI_REGISTRY'));

// Tasks
task('deploy:docker:login','
    echo {{CI_REGISTRY_PASSWORD}} | docker login -u {{CI_REGISTRY_USER}} --password-stdin {{CI_REGISTRY}}
')->desc('Deploy docker login');

task('deploy:docker-compose:pull','
    docker-compose -f {{docker-compose.yml}} pull app
')->desc('Deploy docker-compose pull');

task('deploy:docker-compose:up','
    docker-compose -f {{docker-compose.yml}} up -d
')->desc('Deploy docker-compose up');

task('deploy:docker-compose:restart','
    docker-compose -f {{docker-compose.yml}} restart
')->desc('Deploy docker-compose restart');

task('deploy:docker-compose', function () {
    invoke('deploy:docker:login');
    invoke('deploy:docker-compose:pull');
    invoke('deploy:docker-compose:up');
//    invoke('deploy:docker-compose:restart');
})->desc('Deploy with docker-compose');
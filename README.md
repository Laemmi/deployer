# deployer
Deployer Recipes

## Update composer

    docker run --rm \
    -v "${PWD}:/var/www/html" \
    laemmi/php-fpm:8.1 \
    composer update


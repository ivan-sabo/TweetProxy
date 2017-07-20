Symfony Standard Edition
========================

// installation

set db connection settings in config/parameters.yml

Execute following commands in terminal:
  composer install

  php bin/console doctrine:database:create
  php bin/console doctrine:schema:update --force

add following config values in parameters.yml:
  tweets_number   - number of users tweets that will be shown on user page
  items_per_page  - number of users shown per page
  api_key         - twitter application api key
  secret_key      - twitter application secret key

configure http web server according symfony framework specifications
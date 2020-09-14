### Medialist - Drupal 9

Codebase for the course "7 to 9 Drupal migration path"

## Installation

`ddev start && ddev composer install && ddev exec "cd /var/www/html && npm ci && npm run gulp" && ddev restore-snapshot medialist_20200914220223 && ddev drush cr`

## Login

`ddev drush uli`

# Casestudy

PROJECT SETUP

Step 1. Clone project 
git clone --branch master https://github.com/jagarapu/Casestudy.git

Step 2. Run composer install.
composer install
Step 3. Create database.
php bin/console doctrine:create:database

Run following command for migrations
php bin/console make:migration
php bin/console doctrine:migrations:migrate

Run following command for assets js routes
php bin/console fos:js-routing:dump --format=json --target=public/js/fos_js_routes.json
yarn install
yarn encore dev (that webpack encore is build successfully)


Step 4. CREATE A USER AS ADMIN OR EMPLOYEE
php bin/console techmahindra:user:create

• username:abc
• firstname:def
• email:abc@gmail.com
• role:ROLE_ADMIN | ROLE_EMPLOYEE(you get the username and password)

Step 5:
Run following command to access website
php bin/console server:run (Using webserver bundle)
or
symfony server:start



Technologies used:
• PHP 7.4
•	Backend Framework – PHP Symfony 4
•	Frontend Framework – Bootstrap 4
•	Database - MySQL
•	Test (PHPUnit)
•	Git
• AWS

# Snowtricks

## Back-end Install
- Clone the repository
- In repo folder, run: ```composer install```
- Set database and mailer url in .env file (or .env.local)  
```DATABASE_URL=mysql://username:password@127.0.0.1:3306/snowtricks?serverVersion=5.7```
```MAILER_DSN=gmail+smtp://email@address.com:yourpassword@default```
- Install database: ```php bin/console doctrine:database:create```
- Migrate database structure: ```php bin/console doctrine:migrations:migrate```
- Install fake data: ```php bin/console doctrine:fixtures:load```

## Front-end install
- Get yarn: ```npm install -g yarn```
- Install node modules: ```yarn install```
- Build assets: ```yarn encore production``` or ```yarn encore dev``` or  ```yarn encore dev --watch```
- if you got AJAX routing problems: ```php bin/console fos:js-routing:dump --format=json --target=assets/js/fos_js_routes.json```  
This will generate a JSON with necessary routes for javascript AJAX requests.

## User fixtures
- Logins: **admin@demo.fr**, **user@demo.fr**  
All others users are randomly generated
- All passwords are: **demodemo**

## Code quality
Check at https://codeclimate.com/github/Florkin/snowtricks


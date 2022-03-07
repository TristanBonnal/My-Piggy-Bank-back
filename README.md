# My Piggy Bank
## Install
Clone the repository, run :
```bash
composer install
```
Set your own .env.local 
```
DATABASE_URL="mysql:/user_name:password@127.0.0.1:3306/database_name?serverVersion=mariadb-10.3.25"
JWT_PASSPHRASE=e65cbbbe71ea5677b6199c1821c26d0b
APP_ENV=dev
```

Create database :

```bash
php bin/console doctrine:database:create
```

Migrate database
```bash
php bin/console doctrine:migrations:migrate
```
Load fixtures (fill database with random datas)
```bash
php bin/console doctrine:fixtures:load
```
Generate keypair for lexik (JWT authentication)
```bash
php bin/console lexik:jwt:generate-keypair
```

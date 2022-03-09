# My Piggy Bank

## How to install the project ?

### Clone the repository & run

```bash
composer install
```

### Set your own .env.local

```php
DATABASE_URL="mysql:/user_name:password@127.0.0.1:3306/database_name?serverVersion=mariadb-10.3.25"
JWT_PASSPHRASE=e65cbbbe71ea5677b6199c1821c26d0b
APP_ENV=dev
```

### Create your database

```bash
php bin/console doctrine:database:create
```

### Migrate your database

```bash
php bin/console doctrine:migrations:migrate
```

### Load fixtures in order to fill your databse

```bash
php bin/console doctrine:fixtures:load
```

### Generate a keypair for lexik (JWT authentication)

```bash
php bin/console lexik:jwt:generate-keypair
```

### PHP configuration

In order to run your application, you need to make sure that you have the following modules installed :

* intl
* openssl

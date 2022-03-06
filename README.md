# projet-13-my-piggy-bank-back
## Run this commands to install mypiggybank

```bash
composer install
```
```bash
php bin/console doctrine:database:create
```
```bash
php bin/console doctrine:migrations:migrate
```
```bash
php bin/console doctrine:fixtures:load
```
```bash
php bin/console lexik:jwt:generate-keypair
```



## :pig: My Piggy Bank

My Piggy Bank has been created for those who are struggling saving up and those who are willing to save up for some reason.
It's been built by 5 friends and still improving.

---

### How do I set up the project ? :point_down:

 :bulb: Clone the repository & run

```bash
composer install
```

---

 :fountain_pen: Create and set your own .env.local

* The DATABASE_URL allows __doctrine__ to reach your database

  * "user_name", "password" & "database_name" have to be replaced by your values
  * If you are not using mysql, you must get the right DATABASE_URL on Symfony's website.

* The JWT_PASSPHRASE is mandatory for lexik-bundle to be working and its value is up to you
* Specify your work environment

```php
DATABASE_URL="mysql://user_name:password@127.0.0.1:3306/database_name?serverVersion=mariadb-10.3.25"
JWT_PASSPHRASE=e65cbbbe71ea5677b6199c1821c26d0b
APP_ENV=dev
```

---

 :genie_man: Create your database

```bash
php bin/console doctrine:database:create
```

---

 :dove: Migrate your database

```bash
php bin/console doctrine:migrations:migrate
```

---

 :boom: Load fixtures in order to fill your database

```bash
php bin/console doctrine:fixtures:load
```

---

 :key: Generate a keypair for lexik (JWT authentication)

```bash
php bin/console lexik:jwt:generate-keypair
```

---

 :clipboard: EasyAdmin has been used to build the Back-Office, run

```bash
composer require easycorp/easyadmin-bundle
```

---

 :toolbox: PHP configuration

In order to run your application, you need to make sure that you have the following modules installed :

* intl
* openssl

<details close>

<summary> :rocket: Now, you must be like... :rocket:</summary>


![Alt Text](https://media.giphy.com/media/5GoVLqeAOo6PK/giphy.gif)

</details>

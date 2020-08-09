# tet PHP Developer task

![](tet-php.gif)

Instructions for testing:

Note: Application requires: 
- Symfony CLI
- mysql/mariadb
- Composer
- Node.js

1. Clone or download this repository.

2. Run these commands in the root of the project -
```
composer install
```
and
```
npm install
```

3. In the .env file change this line according to your database info -
```
DATABASE_URL=mysql://username:password@127.0.0.1:3306/table-name?serverVersion=mariadb-10.3.23
```
  
4. Run these commands to create a new database and a table for storing history of ecb exchange rates -
```
php bin/console doctrine:database:create
```
and
```
php bin/console doctrine:migrations:migrate
```


5. Run this custom console command to populate the table with latest ecb rates -
```
php bin/console app:read-ecb-rss
```

6. Lastly, run -
```
npm run dev
```
to build assets, and -
```
symfony server:start
```
to start a web server. 

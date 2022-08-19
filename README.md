# WCOS Marketplace Backend

While the project is still in its early stages, we understand that it still lacks a lot of functionalities and upgrades that could be done, also, currently no proper documentation is available.
We open-sourced it with the hope that developers can collaborate and take full advantage of the features it may have, and build upon it... Help and get helped! 

The whole project is completely open-source, and anyone is free and welcome to use, collaborate, and build on.

### Quick introduction: (WIP)
The backend consists of 4 core components:
- Firebase real-time database for real-time ordering and tracking.
- Mysql database to host any non-real-time data... Orders will start their cycle with firebase and once order is fulfilled data is copied to Mysql and deleted from firebase.
- Laravel backend orchestrating the databases and business logic.
- Rest api's to connect the backend package to any frontend client.

### Features List: (WIP)

## Docker Deployment (recommended)
#### Requirements:
- Docker and docker-compose installed locally. Ref: https://docs.docker.com/compose/install/compose-desktop/

#### 1. Quick Fire-up using script:
Rename `env.docker.example` file to `.env` , no need to fill in firebase credintials now to get started... change environmental variables as per your need. 
```
cp .env.docker.template .env
```

Run deployment script:
```
./deploy-docker.sh
```

#### 2. Manually:

Deploy containers
```
docker-compose up -d && docker-compose logs -f
```

Fix `storage` folder permissions:
```
docker-compose exec wcos-backend chmod -R 777 /var/www/storage
```

Composer install:
```
docker-compose exec wcos-backend composer install
```

Artisan:
```
docker-compose exec wcos-backend chmod -R 777 storage
docker-compose exec wcos-backend php artisan key:generate
docker-compose exec wcos-backend php artisan db:seed --class=PermissionsTableSeeder
```  

Done. system may be accesses at `http://localhost:2222/` (or the specified port in docker-compose.yml file [if modified])

#### System Access

Login information for admin user:
```
username : admin@demo.com

password : 123456
```


#### Extra commands:

For any commands inside the laravel container:
```
docker-compose exec wcos-backend #command#
```
  

Or SSH directly inside the container:
```
docker-compose exec wcos-backend bash
```

For any commands inside the database container:
```
docker-compose exec wcos-db #command#

docker-compose exec wcos-backend bash
```

### Docker Strategy:
The deployment strategy was done in a way to comfort developers wether in production or development environments.

The container is used as an out-of-the-box server that has all the dependencies and compatibility issues pre-built and fixed, allowing the container to be like a server to the project, while allowing developers to modify source-code and see changes in real-time without the need of any expertise in docker systems.

The code directory is mapped to the docker-container, so any change you make in the files will immediately be reflected inside the container... making the container practically just an engine running your code with all of its required dependencies and server enhancements, in any platform you may have, and without the need to make any changes in your local computer environments. 

If you have a development environment already installed, you can still pass Laravel commands in the code directory itlsef and changes will be reflected inside the container immediately.

The images are pre-build and uploaded in the docker-registry, so develoepers can download them directly without the need to build them themselves.

# 2. Standard Deployment:

#### Requirements
- PHP = 7.3
- GRPC installed locally. Ref: https://cloud.google.com/php/grpc

Clone repository
```code
https://github.com/we-code-open-source/marketplace-backend.git
```

Import database dump into your local Mysql
```code
./deploy/wcos-dump.sql
```

Rename .env.template to .env
``` code
cp .env.template .env
```

Edit databse configuration in .env file
``` code
DB_DATABASE=name_of_database

DB_USERNAME=user_of_database

DB_PASSWORD=password_of_database
```

Install composer dependencies
```code
composer install
```
note: make sure php's grpc extension is already installed before running `composer install`.
 
Generate an application key
```code
php artisan key:generate
```

Finally run the server
```code
php artisan serve
```

Login information for admin user
```code
username : admin@demo.com
password : 123456
```
note : make sure you did step number 2

Instead of inserting permissions manually , you can load all permissions to system (Database) depending on routes names by executing the command below
``` code
php artisan db:seed --class=PermissionsTableSeeder
```

More documentation and enhacements are being worked upon and will be updated regularly.

## Road Map (initial - open to suggessions and contributions)
- Upgrade Laravel version to 9.x.
- Plug-in System.
- Migrate to Appwrite.io instead of Firebase/Mysql. This will allow for a single high performance real-time database/document-based db with all the features firebase offers, and better yet, for free, making the project completely free to use and deploy in production. (Could be coded as a plugin).

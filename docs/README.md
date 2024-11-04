# About
This repository contains everything you need to get started to run a 
Laravel application with Docker as fast as possible-- using Ngninx, MySQL, and PHP 8.2.

# Setup Requirements
docker

git

laravel cli installer


## Step 1: Create the project directory & Clone this repo
```sh
mkdir ~/example-path/appex-project/
cd ~/example-path/appex-project/
git clone git@github.com:clemmerwis/local-docker-laravel-dev.git
```

## Step 2: Edit the docker-compose.yml
select all occurences of "appex" and edit to match the name of your project.

Note! Don't include hyphens in your database environment
```sh
This is good!
  mysql:
    image: mysql:latest
    restart: unless-stopped
    environment:
      - MYSQL_ROOT_PASSWORD=my_secret_pw
      - MYSQL_DATABASE={appname}
      - MYSQL_USER={appname}_user
      - MYSQL_PASSWORD={appname}_pass

This is bad. Don't use hyphens!
    environment:
      - MYSQL_ROOT_PASSWORD=my-secret-pw
      - MYSQL_DATABASE=app-example
      - MYSQL_USER=app-example_user
      - MYSQL_PASSWORD=app-example_pass
```
Note! In your Laravel .env file, change DB_HOST to mysql (the service name) rather than 127.0.0.1 (or other address) because Docker Compose creates a network where services can reference each other by their service names. This goes for Redis too obviously.
```sh
DB_HOST=mysql
REDIS_HOST=redis
```

Note! for papercut or mail testing in general you can use Docker's special DNS name host.docker.internal which points to your host machine
```sh
MAIL_HOST=host.docker.internal
```

Note! Update all volume paths to match your local project path
```sh
volumes:
  - ~/myComp/web-work/local-docker-dev/appex-project/appex:/var/www
  
  Example:
  - ~/example-path/appex-project/appex:/var/www
```

## Step 3: Clone your repo into the project directory & set up Vite config
```sh
cd ~/example-path/appex-project/
git clone git@github.com:your-name/your-repo.git

Or

cd ~/example-path/appex-project/
laravel new appex
```

Also make sure db creds in `.env` match those in the docker-compose.yml `mysql environment variables`.
```sh
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=appex
DB_USERNAME=appex_user
DB_PASSWORD=appex_pass
```

Once you your app is in the project directory, replace the vite-config.js with the one that comes with this repo.

## Step 4: Build with Docker 
docker compose -p {appname} build

```sh
cd ~/example-path/appex-project/dockerdir
docker compose -p appex build 
```

Once the containers are built, you can launch the newly created Docker environment.

```sh
cd ~/example-path/appex-project/
docker compose -p appex up -d
```

After the above command, there should be 4 containers running: Nginx, MySQL, App, & Redis.

## Step 4: Install & Start the App
Now enter the app container

```sh
docker compose ps
```

The command above will display a list of running containers. Copy the id of the app container.

```sh
docker exec -it {app_container_id} bash
```

once inside the var/www directory, run the following commands in order.

```sh
composer install
npm install --save-dev @vitejs/plugin-vue
php artisan key:generate
php artisan migrate:fresh --seed
npm run dev
```

The last command should start the app on localhost. Open the welcome.blade.php
and test if Vite is HMR is working. You should see a loading symbol in the browser tab if the HMR is working, it may take longer than expected.

## Final Notes
Stop the project

```sh
docker compose -p {appname} stop 
```

Start the project

```sh
docker compose -p {appname} start 
```

Destroy the containers

```sh
docker compose -p {appname} down 
```

Build the containers

```sh
docker compose -p {appname} build --no-cache
```
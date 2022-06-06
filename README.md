# Puskom
#### _

## Tech
- Nuxt 2.14  (VueJS)
- Laravel 9
- Using redis
- Using Postgres database (Docker Installation)

## Installation with Docker

This project is very easy to install and deploy in a Docker container.

By default, the Docker will expose port `9004`, so change this within the `docker-compose.yml` and several `Dockerfile` if necessary. When ready, simply use the Dockerfile to
build the image.

- In the *app-laravel-nine* directory, copy `.env.example` to `.env`

- In the *nuxt-adminlte* directory, copy `.env.example` to `.env`, then edit `.env` 

##### _in the root project-directory_
```sh
$ docker-compose up -d --build
```

This will create and run the app images and pull in the necessary dependencies.
Verify the deployment by navigating to your server address in
your preferred browser.

```sh
http://localhost:9004

username: admin
email: admin@mail.com
password: admin123
```

## Installation Backend (Manually)

- `composer install`
- Copy `.env.example` to `.env`, then edit `.env` to set your any config
- `php artisan migrate --seed`

## Installation Frontend (Manually)

- `npm install -g nuxt`
- Copy `.env.example` to `.env`, then edit `.env` to set your `APP_URL` (the url to your backend (Laravel) application)
- `npm install`

## Usage if using manual installation
### Development

on frontend directory
```bash
npm run dev
```
on backend directory
```bash
php artisan serve
```
You can access your application at `http://localhost:3000`.




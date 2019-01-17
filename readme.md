# Scraping ( Laravel + Goutte Library )

Obtain the reviews of a restaurant booking website through webscraping; for this, I used the PHP Laravel 5.7 framework and the Goutte library to perform scraping.

## Disclaimer

This repository/project is intended for Educational Purposes ONLY. It is not intended to be used for any purpose other than learning, so please do not use it for any other reason than to learn about DOM scraping!!

## Dependencies

- **[Laravel 5.7](https://laravel.com/docs/routing)** requirements

## Installation

Clone project

```bash
git clone https://github.com/moiseshp/reviews-scraping.git
```

Go to the project folder

```bash
cd reviews-scraping
```

Install the dependencies with **[composer](https://getcomposer.org/)** (Laravel Goutte)

```bash
composer install
```

## Migration

Configure the database and its credentials for the project in .env

```bash
DB_DATABASE=reviews
DB_USERNAME=example
DB_PASSWORD=secret
```

Run artisan database

```bash
php artisan migrate
```

## Running

The artisan command does the following:
- First, you get a list of all the **possible restaurants** that the platform has.
- Second, you go through each restaurant obtained and keep **all the reviews** that the restaurant had.
- Finally, save the comments for each restaurant in the table **reviews**

```python
php artisan scraping:reviews_restorando
```

You can check the code, in detail, in **App\Console\Commands\Scraping\Reviews**

## Referencias

- **[Laravel-Goutte](https://github.com/dweidner/laravel-goutte)**
- **[Vegibit](https://vegibit.com/php-simple-html-dom-parser-vs-friendsofphp-goutte/)**
- **[Fastfwd](https://www.fastfwd.com/website-scraper-using-laravel-goutte/)**
- **[Readthedocs](https://goutte.readthedocs.io/en/latest/)**

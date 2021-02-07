<div align="center">

  <h1 align="center">Przypomijanka - Backend API</h1>

  <p align="center">
    Application for sending reminders to users
  </p>
</div>



<!-- TABLE OF CONTENTS -->
## Table of Contents

* [Built With](#built-with)
* [Getting Started](#getting-started)
    * [Installation](#installation)
    * [Testing](#testing)
    * [Dummy data](#dummy-data)
* [Development](#development)
* [Api Documentation](#api-documentation)
* [Contact](#contact)


## Built With
* [Symfony 5.2](https://symfony.com/doc/current/setup.html)

## Getting Started

To install the application you will need:

* PHP >= 7.4
* git
* composer
* symfony flex

### Installation

1. Go to the folder where you want to create the project and clone the repository
```sh
git clone https://github.com/Janczur/PrzypomijankaBackend.git
```
2. Install dependencies
```sh
composer install
```
3. Configure environment  
   [Information about configuring environment](https://symfony.com/doc/current/configuration.html#overriding-environment-values-via-env-local)  
   copy ".env" file to ".env.local.dev" and provide valid DSN configuration   
```dotenv
DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name"
MESSENGER_TRANSPORT_DSN=doctrine://default
MAILER_DSN=smtp://user:pass@smtp.example.com:port
MAIL_FROM=site@email.pl
ADMIN_MAIL=admin@email.pl
```

### Testing

For tests run in the main project directory
```sh
php bin/phpunit
```

### Dummy data
If you want to populate database with dummy data run:
```sh
php bin/console doctrine:fixtures:load
```

## Development

To start local server type in your project directory
```sh
symfony serve
```

## Api Documentation
Coming soon

## Contact

Jan Przybysz - jan.j.przybysz@gmail.com
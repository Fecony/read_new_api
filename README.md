# News API

### News Providers

Acquire API token from:

- [News API](https://newsapi.org/)
- [The Guardian](https://open-platform.theguardian.com/documentation/)
- [The New York Times](https://developer.nytimes.com/apis)

### Prerequisites

Things you will need:

-   [Docker](https://docs.docker.com/get-docker/)

Things you might need to test API:

-   [Postman](https://www.postman.com/downloads/)
-   [Insomnia](https://insomnia.rest/download)

> Make sure you have all required PHP extensions installed on your local
> machine https://laravel.com/docs/10.x/deployment#server-requirements

Clone the project

```bash
git clone git@github.com:Fecony/read_new_api.git
```

Go to the project directory

```bash
cd read_new_api
```

Copy .env.example file to .env on the root folder.

```bash
cp .env.example .env
```

#### Docker  🐳

By default, application is configured to run in Docker container. You don't have to change any environment configuration
setting.

> This command will run Docker container to install application dependencies
> You can refer to Laravel
> Sail [docs](https://laravel.com/docs/10.x/sail#installing-composer-dependencies-for-existing-projects) for other useful
> commands!

```bash
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v $(pwd):/var/www/html \
    -w /var/www/html \
    laravelsail/php82-composer:latest \
    composer install --ignore-platform-reqs
```

To run app in Docker container make sure that Docker is running.

```bash
./vendor/bin/sail up -d
```

Run `./vendor/bin/sail artisan key:generate` to generate app key.

After you application is running in Docker container run `./vendor/bin/sail artisan migrate` to run migration files.

### Fetching news

News sources are scraped using Commands, before running them setup News provider API keys in .env file.
Then you can following run commands to fetch news:

- `app:fetch-news-api` - Fetch [News API](https://newsapi.org/)
- `app:fetch-the-guardian-news` - Fetch [The Guardina News](https://open-platform.theguardian.com/)
- `app:fetch-the-new-york-times` - Fetch [The New York Times](https://developer.nytimes.com/apis)

### Troubleshooting - Common Problems

This page lists solutions to problems you might encounter. Here is a list of common problems.

#### Access denied for user 'sail@172.20.0.3'... | Docker 🐳

-   Try to run `./vendor/bin/sail down --rmi all -v`. It will remove all images used by any service and remove named
    volumes.
-   (optional) You might run `./vendor/bin/sail build --no-cache` to build image before running next command
-   Then run `./vendor/bin/sail up -d` again to build container.

#### Cannot start service mysql: Ports are not available: listen tcp 0.0.0.0:3306: bind: address already in use

Most likely you have running mysql service locally. There are 2 solutions to this issue:

-   You have to stop your local mysql service to make port 3306 available for docker
-   Use `FORWARD_DB_PORT` in your .env to use different port for docker port binding -`FORWARD_DB_PORT=3307`

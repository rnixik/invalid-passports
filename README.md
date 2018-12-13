# Passport validation service
Validates passport's series and number using FMS database.

## API

### Request

`GET /series=SERIES&number=NUMBER`

Where `SERIES` is 4  digits, `NUMBER` is 6 digits of passport.

### Responses:

#### Code 200

* `{"result":"valid"}`
* `{"result":"invalid"}`

#### Code 400, 500

`{"error":"Details"}`


## Run

### Requirements:

* docker
* docker-compose
* 4GB RAM minimum

### How to start:

1. Copy `docker-compose.override.example.yml` to `docker-compose.override.yml`
2. Edit `docker-compose.override.yml` for your preferences.
3. Run `docker-compose up -d --build`
4. Run `docker-compose exec app composer install`

## Update data storage

`docker-compose exec -T app bash bin/download-file.sh`
`docker-compose exec -T app php bin/update.php`

Should be added to cron.

## Implementations

There are some implementations which were developed as experiments:

* Redis - using redis as storage
* Shmop - using shared memory as storage (one big string)
* Include - using tmpfs and `include` php array
* IncludeSeries - using tmpfs and `include` php arrays by series

Default implementation can be changed in src/Application.php.  
Run update after changing.

## Testing


```
docker-compose exec app vendor/bin/phpunit tests/
```


## Useful things

### Benchmarking

```
docker-compose exec app php bin/benchmark.php
```

### API for caching:

* `GET /reset-cache` - reset cache of application
* `GET /prepare-cache` - prepare cache of application

## License

The MIT License

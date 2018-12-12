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

#### Code 400

`{"error":"Details"}`


## Run

### Requirements:

* docker
* docker-compose

### How to start:

1. Copy `docker-compose.override.example.yml` to `docker-compose.override.yml`
2. Edit `docker-compose.override.yml` for your preferences.
3. Run `docker-compose up -d --build`
4. Run `docker-compose exec app composer install`


### Testing


```
docker-compose exec app vendor/bin/phpunit tests/
```


## License

The MIT License

# AdvisorEngine Trading App Exercise
[Coding Task](_docs/Coding%20Task.md)

## Getting Started
```
# Start App and DB
docker-compose up -d
# DB Migrations
docker-compose exec app php bin/console doctrine:migrations:migrate
# Load Data Fixtures
docker-compose exec app php bin/console doctrine:fixtures:load --env=dev
```

## Create Stock Transactions
Update `host` and `port`
```
curl --request POST \
  --url http://{host}:{port}/api/v1/portfolio/transactions \
  --header 'Accept: application/json' \
  --header 'Content-Type: application/json' \
  --header 'x-api-key: b6b576a9-010e-4e83-82a8-4d9c6d699b77' \
  --data '{\n	"symbol": "AAPL",\n	"amount": 2,\n	"date": "2017-12-21",\n	"type": "sell"\n}'
```

## Get Portfolio
Navigate to the exposed nginx port from docker-compose (eg. http://0.0.0.0:1234/)
`docker-compose ps`

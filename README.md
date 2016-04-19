# poc-slim
POC Rest API using [Slim](http://www.slimframework.com/) that allows you to create, update and retrieve Person objects stored in a MongoDB NoSQL database.

## Configuration
Default MongoDB config in /src/public/index.php file:

```
$client = new MongoDB\Client("mongodb://localhost:27017");
```

## How to run
```
cd poc-rest-simple/src/public/
php -S localhost:8081
```

## Rest API examples
GET
```
curl http://localhost:8081/persons
curl http://localhost:8081/persons/{ID}
```

POST
```
curl -i -X POST -H "Content-Type:application/json" -d '{  "firstName" : "Marty",  "lastName" : "McFly" }' http://localhost:8081/persons
```

PUT
```
curl -X PUT -H "Content-Type:application/json" -d '{ "firstName": "Emmett", "lastName": "Brown" }' http://localhost:8081/persons/{ID}
```

DELETE
```
curl -X DELETE http://localhost:8081/persons/{ID}
```

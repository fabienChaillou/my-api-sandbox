# Starter Docker with service:

* ngnix: 1.15
* php_fpm: 7.3
* node: 12

### Help
``
run make 
``


### Install
```Shell
make infra-up
make install 
```

### jwt
passphrase: `sFN}+6A"5;PB9}hV`

Generate a token:
```shell
php bin/console bin/console lexik:jwt:generate-token {username}
````
Other solution for generate a token 
```shell
curl -X POST -H "Content-Type: application/json" http://localhost:80/login_check -d '{"username":"admin"}'
```

then:
#### Test with REST
* Run `http://localhost/docs`
* Click on `Authorize`
* Add `Bearer {TOKEN}`

#### Test with GraphQL
Run `http://localhost/docs/graphiql`
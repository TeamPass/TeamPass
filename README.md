# Development

## App

### Development

```bash
sencha app watch
```


### Production

#### Building

```bash
sencha app build teampass production
sencha app build teampass-pink production
```



## Dist

### running Tests

```bash
./bin/phpcs --ignore=DistributionPackages/TeamPass.Core/Migrations/Mysql --standard=PSR12 DistributionPackages/
```
![TeamPass](https://github.com/TeamPass/TeamPass/workflows/TeamPass/badge.svg) [![Build Status](https://scrutinizer-ci.com/g/TeamPass/TeamPass/badges/build.png?b=master)](https://scrutinizer-ci.com/g/TeamPass/TeamPass/build-status/master) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/TeamPass/TeamPass/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/TeamPass/TeamPass/?branch=master)

# Development

## App

### Development

```bash
export PATH=~/bin/Sencha/Cmd/:$PATH
export OPENSSL_CONF=/dev/null
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

# Testeroid


It is simple plugin to getting auto tests and TDD by WP CLI

# why?

It doesn't always make sense to configure complex PHP Unit for simple testing

| compare | Testeroid | PHP Unit | PestPHP |
| --- | --- | --- | --- |
| Time to install and config | 1 min | 1-2 weeks | 1-2 weeks |
| Learning curve | 1 day | 1-2 months | 1-2 months |
| WP CLI support | + | - | - |
| modern syntax | + | - | + |
| simple bootstrap | + | - | - |
| universal testing for App, Plugin or Theme | + | - | - |

# how?

## base
- make folder `tests` in plugin, or theme, or for whole site
- set constant `TESTEROID_TESTS_PATH` in wp-config.php to path of tests like `define('TESTEROID_TESTS_PATH', __DIR__ . '/path/to/tests/*.php')`

## write tests

like PestPHP

```
<?php
//file ./tests/SomeComponent.php

namespace App\Tests\SomeComponent;

test('smoke test', function(){

    $value = check_data(5);

    if($value === 10){
        return true;
    } else {
        return false;
    }

}, 1);

function check_data($x){
    return $x + 5;
}
```

## run
```
cd ./path/to/wp
wp tests
```
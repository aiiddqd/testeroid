# Testeroid

It is simple plugin to getting auto tests and TDD by WP CLI

inspired by Laravel & https://pestphp.com/

support and questions https://github.com/aiiddqd/testeroid/issues

# why?

It doesn't always make sense to configure complex PHP Unit for simple testing

| Comparison | Testeroid | PHPUnit | PestPHP |
| --- | --- | --- | --- |
| Time to install and config | 1 min | 1-2 weeks | 1-2 weeks |
| Learning curve | 1 day | 1-2 months | 1-2 months |
| modern syntax | + | - | + |
| WP CLI support | + | - | - |
| simple bootstrap | + | - | - |
| universal testing for App, Plugin or Theme | + | - | - |


# how?

## installation

- install plugin `wp plugin install https://github.com/aiiddqd/testeroid/archive/main.zip --activate --force`
- add tests via filter `testeroid_tests`. 
example:
```php
add_filter('testeroid_tests', function($tests){
    $tests[] = [
        'name' => 'test 1',
        'callback' => function(){
            return true;
        }
    ];
    return $tests;
});

run:
```
wp testeroid
```
or
```
wp testeroid --case="test 1"
```


# Todo
- [ ] improve CLI UI
- [ ] test GitHub Actions and auto testings for PRs
- [x] test seeding and import test data

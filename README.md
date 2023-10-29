# Testeroid

It is simple plugin to getting auto tests and TDD by WP CLI

inspired by Laravel & https://pestphp.com/

demo https://github.com/wpcraft-ru/wooms/tree/master/tests

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
- install plugin `wp plugin install https://github.com/uptimizt/testeroid/archive/main.zip --activate --force`
- make folder `tests` in plugin, or theme, or for whole site
- set constant `TESTEROID_TESTS_PATH` in wp-config.php to path of tests 
  - exmaple `define('TESTEROID_TESTS_PATH', __DIR__ . "/path/to/tests/")`
  - via wp cli `wp config set TESTEROID_TESTS_PATH '__DIR__ . "/path/to/tests/"' --raw`

## write tests

like PestPHP

### feature test example
```
//file ./tests/SomeComponent.php

namespace App\Tests\SomeComponent;

use function Testeroid\{test, transaction_query};


transaction_query('start');

test('simple test', function(){

    $post_data = [
        'post_title'    => 'test 1',
        'post_content'  => 'test',
        'post_status'   => 'publish',
    ];

    // Insert the post into the database
    $post_id = wp_insert_post( $post_data );
    $post = get_post($post_id);



    if($post->post_title === 'test 1'){
        return true;
    } else {
        return false;
    }

}, 1);


transaction_query('rollback');
```


### unit test example
```
<?php
//file ./tests/SomeComponent.php

namespace App\Tests\SomeComponent;

use function Testeroid\test;

test('smoke test', function(){

    $value = check(5);

    if($value === 10){
        return true;
    } else {
        return false;
    }

}, 1);

function check($x){
    return $x + 5;
}
```



## run
```
cd ./path/to/wp
wp test
```


# Todo
- [x] test seeding and import test data
- [x] improve CLI UI
- [ ] test GitHub Actions and auto testings for PRs

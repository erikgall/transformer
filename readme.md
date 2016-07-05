# Laravel Model Transformer

This package was created to be used inside of a Laravel application that utilizes an API to deliver data. This package allows you transform model's directly from the model without clogging up the model class/file. This package also make's using Eloquent relationships easy to transform.

This package also allows for easy handling of including transformed Eloquent model relationships.

## Getting Started

These instructions will get you get you up and running and using the package inside of your Laravel application.

### Prerequisities

The package was developed using Laravel 5.2 and PHP 7 so they're may be code that is not compatible with PHP 5.

```
- PHP >= 7
- Laravel >= 5.2
```

### Installing

To install the package inside of your Laravel app, please follow the steps below.

Step 1: Run the following command from your project root.
```
composer require erikgall/transformer
```

Step 2: Add the package's service provider to your provider's array in you `app.php` config file.

```
EGALL\Transformer\TransformerServiceProvider::class
```

Step 3: Create a transformers directory in your `app/` directory or wherever you would like.

Step 4: Any models use wish to use a transformer with should have the `Transformable` interface/contract added to their declaration and should also use the `TransformableModel` trait.


```php
namespace App;

use App\Transformers\UserTransformer;
use Illuminate\Database\Eloquent\Model;
use EGALL\Transformer\Contracts\Transformable;
use EGALL\Transformer\Traits\TransformableModel;

class User extends Model implements Transformable {

    use TransformableModel;

    // Used in example below
    public function school()
    {

        return $this->belongsTo(School::class);

    }

    protected function transformer() {

        // UserTransformer example below.
        return new UserTransformer($this);
    }

}

```


Examples below:

**User Model Transformer Class**

```php

namespace App\Transformers;

use EGALL\Transformer\Transformer;

class UserTransformer extends Transformer {

    protected $keys = ['id', 'name', 'address'];

    // Create a custom attribute getter method like you would
    // in an eloquent model using the syntax get`AttributeName`Attrbute.
    public function getNameAttribute()
    {

        return $this->model->first_name . ' ' . $this->model->last_name;

    }

}

```

### Using the User Model Transformer from a controller.

```php

namespace App\Http\Controllers;

use Guard;

class UserController extends Controller {

    protected $user;

    public function __construct(Guard $guard)
    {

        $this->user = $guard->user();

    }

    public function me()
    {

        // Get only the user transformer data
        $data = $this->user->transform();

        // return an array with a transformed relationship.
        // if the relationship does not implement the transformable class
        // the transformer will call the toArray() on the model.
        $data = $this->user->transformer()->with('course')->transform();

    }

    public function dependencyInjectionExample(\EGALL\Transformer\Contracts\Transformer $transformer)
    {

        return $transformer->model($this->user)->keys(['id', 'first_name', 'last_name])->transform();

    }

}

```




## Running the tests

Install the package and install it's dependencies. After, that is complete and once inside the package's root, run the following command:

```
vendor/bin/phpunit
```

## Built With/Using

* Laravel
* PHPUnit
* PhpStorm

## Contributing

Please read [CONTRIBUTING.md](CONTRIBUTING.md) for details on our code of conduct, and the process for submitting pull requests to us.

## Versioning

We use [SemVer](http://semver.org/) for versioning. For the versions available, see the [tags on this repository](https://github.com/erikgall/transformer/tags).

## Authors

* **Erik Galloway** - [Flip Learning](https://github.com/erikgall)

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details
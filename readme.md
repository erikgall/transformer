# Laravel Model Transformer

Transform Laravel Eloquent models into an array of specified keys with the option of including nested relationships as their transformed model array.


## The Reason

This package was created to ease complexity, simplify model's, while allowing for them to be quickly transposed into an array of specified keys.

But turning an eloquent model into an array is easy right? You only have to call the `toArray()` method. The shortfall comes when you want the final array to only have specific fields/keys, as well as a nested relationship. You end up transforming the model inside of your controller, specifying each and every key and value the array should have.

### Example

#### Without Laravel Model Transformer

```php
$user = App\User::with('school')->first();

$data = $user->toArray();

/**
 * What we get from the broad example is something like:
 *   [
 *      'id' => 1,
 *       'email' => 'example@example.com',
 *       'password' => '.....',
 *       'first_name' => 'John',
 *       'last_name' => 'Doe',
 *       'school_id' => 1,
 *       'created_at' => '...',
 *       'updated_at' => '...',
 *       'school' => [
 *           'id' => 1,
 *           'name' => 'Example University',
 *           'book_price' => 6000,
 *           'created_at' => '...',
 *           'updated_at' => '...'
 *       ]
 *   ];
 */
```

#### With Laravel Model Transformer

```php
$user = App\User::first();
$user->transformer()->with('school')->transform();
```

And it returns:

```php
[
    'id' => 1,
    'email' => 'example@example.com',
    'name' => 'John Doe',
    'school' => [
        'id' => 1,
        'name' => 'Example University'
    ]
]
```

## Getting Started

These instructions will get you get you up and running and using the package inside of your Laravel application.

### Prerequisities

The package was developed using Laravel 5.2 and PHP 7 so there may be incompatibilities with PHP 5.

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

Step 3: Create a transformers directory inside of the directory where your model files live. Example using the location of a User model and its transformer class:

    - app/User.php -> app/transformers/UserTransformer.php
    - app/Models/User.php -> app/models/transformers/UserTransformer.php

    * The Transformers directory must be in the same directory as you models... Only if you wish to use the automatic transformer class finder. Otherwise you must specify the model's transformer class/full namespace.

Step 4: Any models use wish to use a transformer with must implement the `EGALL\Transformer\Contracts\Transformable` interface/contract. You should also include the trait `EGALL\Transformer\Traits\TransformableModel` inside of your model classes.


```php

namespace App;

use App\Transformers\UserTransformer;
use Illuminate\Database\Eloquent\Model;
use EGALL\Transformer\Contracts\Transformable;
use EGALL\Transformer\Traits\TransformableModel;

class User extends Model implements Transformable {

    use TransformableModel;

    // Only use if your Transformer directory does not sit in the same directory
    // as the model file.
    // protected $transformer = 'Acme\Transformers\UserTransformer';

    // Used in example below
    public function school()
    {

        return $this->belongsTo(School::class);

    }

}

```


Example below:

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

    public function dependencyInjectionExample(\EGALL\Transformer\Contracts\CollectionTransfomer $transformer)
    {

        // Collection example
        $transformer->collection(User::all())->keys('id', 'name')->with('school')->transform();

    }

    public function diExampleTwo(\EGALL\Transformer\Contracts\Transformer $transformer)
    {

        return $transformer->item($this->user)->keys(['id', 'first_name', 'last_name])->transform();

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
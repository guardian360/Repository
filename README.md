Guardian360 Repository
=====================

A base repository for Eloquent models.

Requirements
------------

* PHP >=7.0.0
* PHP MongDB driver (optional)

Installation
------------

Install via composer.

```sh
$ composer require guardian360/repository
```

Usage
-----

First, you should create your repository class. You can do this manually or by
using Artisan by doing `$ php artisan make:repository UserRepository`. Your
repository _must_ extend `\Guardian360\Repository\AbstractRepository` and
implement the `model()` method.

```php
<?php

namespace App\Repositories;

use App\User;
use Guardian360\Repository\AbstractRepository as Repository;

class UserRepository extends Repository
{
    /**
     * Specify the model's class name.
     *
     * @return string
     */
    public function model()
    {
        return User::class;
    }
}
```

By implementing the `model()` method, you are telling the repository what model
class you want to use. Next, in our case, it seems only logical to have a User
model. Why don't we create one?

```php
<?php

namespace App;

use Illuminate\Database\Eloquent\Model

class User extends Model
{
    //
}
```

Finally, you may use the repository in your controller, or anywhere else
really.

```php
<?php

namespace App\Http\Controllers;

use App\Repositories\UserRepository;

class UserController extends Controller
{
    /**
     * @var \App\Repositories\UserRepository
     */
    protected $users;

    /**
     * @return void
     */
    public function __construct(UserRepository $user)
    {
        $this->users = $users;
    }

    /**
     * Display a listing of the users.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json($this->users->all());
    }
}
```

Examples
--------

Fetch a list of all users.
```php
$this->users->all();
```

Find a user.
```php
$this->users->find(1);
```

Find a user by an attribute.
```php
$this->users->findBy('name', 'Joe');
```

Find all users by an attribute.
```php
$this->users->findBy('role', 'admin');
$this->users->findBy('role', ['admin', 'guest']);
```

Create a new user.
```php
$this->users->create($request->all());
```

Update an existing user.
```php
$this->users->update($user, $request->all());
$this->users->update(1, $request->all());
```

Delete an existing user.
```php
$this->users->delete($user);
$this->users->delete(1);
$this->users->delete([1, 2]);
```

Specifications
--------------

Specifications allow you to apply very specific conditions to the repository's
query. You may use Artisan to generate a Specification class for you, using
`$ php artisan make:specification UserIsAdmin`, or you may do so manually.
Your specification _must_ satisfy the
`\Guardian360\Repository\Contracts\SpecificationContract` contract.

```php
<?php

namespace App\Specifications;

use Guardian360\Repository\Contracts\SpecificationContract as Specification

class UserIsAdmin implements Specification
{
    /**
     * Apply the specification.
     *
     * @param  mixed  $query
     * @return mixed
     */
    public function apply($query)
    {
        return $query->where('role', 'admin');
    }
}
```

Then, you may push the Specification to the repository in your controller. You
may use as many Specifications as you like.

```php
<?php

namespace App\Http\Controllers;

use App\Specifications\UserIsAdmin;
use App\Repositories\UserRepository;

class UserController extends Controller
{
    /**
     * @var \App\Repositories\UserRepository
     */
    protected $users;

    /**
     * @return void
     */
    public function __construct(UserRepository $user)
    {
        $this->users = $users;
    }

    /**
     * Display a listing of the admins.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $admins = $this->users->pushSpec(new UserIsAdmin)->all();

        return response()->json($admins);
    }
}
```

Credits
-------
This package is inspired by the following awesome packages:
- https://github.com/bosnadev/repository
- https://github.com/andersao/l5-repository
- https://github.com/ollieread/laravel-repositories

Thanks guys, I could not have done this without you. :)

# Laravel hmac http

Server-to-server service consuming with hmac authentication.

## Installation

```
composer require siewwp/laravel-service-consumer:dev-master
```

## Usage

### Binding your key

Your should bind your app id and secret at your `ServiceProvider`. 

Refer to [Hmac http client](https://github.com/siewwp/php-hmac-http) for more information.

Refer to [Laravel Container](https://laravel.com/docs/5.6/container) for more information.

### Handling webhook notification

If the service host is notifying event using webhook, you may define your webhook handler in your controller and extend 
to the webhook controller like so: 

```php
<?php

namespace App\Http\Controllers;

use Siewwp\LaravelServiceConsumer\Http\Controllers\Webhook;
use App\Http\Controllers\Controller;

class InvoiceController extends Controller
{
    public function handleInvoicePaid($payload) {
        // ...
    }
}
```

Or you can use the `Siewwp\LaravelServiceConsumer\HandleWebhook` trait in your controller 

```php
<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Siewwp\LaravelServiceConsumer\HandleWebhook;

class InvoiceController extends Controller
{
    use HandleWebhook;

    public function handleInvoicePaid($payload) {
        // ...
    }
}
```

The name of the webhook method should be `'handle' + 'CamelCase'` of the type of event notification. In the example above, 
is to handle `InvoicePaid` type event. 

Refer to [laravel-service-host](https://github.com/siewwp/laravel-service-host) for more information.

After that, you may register the webhook controller it on `RouteServiceProvider.php` file. 

```php
<?php
    // ...
    public function boot()
    {
        // ...
        Route::post(
            'tenant/webhook',
            '\App\Http\Controllers\InvoiceController@handleWebhook'
        );
    }
    ...
```

> You can also register it on your `web` routes but you may need to exclude CSRF middleware

## TO DO

TESTING
<?php

namespace Siewwp\LaravelServiceConsumer\Http\Controllers\Webhook;

use Illuminate\Routing\Controller as BaseController;
use Siewwp\LaravelServiceConsumer\HandleWebhook;

class Controller extends BaseController
{
    use HandleWebhook;
}
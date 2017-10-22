api-client-bundle
=================

A Client API to communicate with WebServices

### 1. Composer
Add the dependency to your composer.json
```bash
    "geny/api-client-bundle": "dev-develop"

```

### 2. Enable it in the AppKernel.php
Enable the bundle :
```bash
    new Geny\ApiClientBundle\GenyApiClientBundle(),

```

### 3. Config
In the config.yml files , declare your own :
```bash
geny_api_client:
    api:
        your_api_name:
            endpoint_root: URL API
            security_token: your token (in progress)
```

### 4. Work with Container
You can access to your API server thanks to container in SF : 

```php

$this->container->get('api.your_api_name')->get('/posts');
$this->container->get('api.your_api_name')->post('/posts',array('title'=>'test'));
$this->container->get('api.your_api_name')->put('/posts/1',array('title'=>'test'));
$this->container->get('api.your_api_name')->delete('/posts/1');
```

### 4. Additionals Tips
You can use your own Rest Client and you need to implement RestApiClientInterface  : 

```php
use Geny\ApiClientBundle\Http\Rest\RestApiClientInterface;

class MyClient  implements RestApiClientInterface 
```
```bash
geny_api_client
    api:
        your_api_name:
            client: Namespace of your Client Example => 'AppBundle\Http\MyClient'
```

### 6. New Features

25/01/2017 => Redis Cache With TTL
15/02/2017 => Create a file in log folder for each client / DataCollector added to profile each request


```bash
geny_api_client:
    api:
        your_api_name:
            endpoint_root: URL API
            // new feature added:
            redis:
                client: # Redis Server Adresse
                port: #  Redis Server Port
                cache: # TTL in seconds ex : 3600 for 1 hour
            log: # Boolean , Log each client
            profiler: # Boolean , Add collector to profiler
```

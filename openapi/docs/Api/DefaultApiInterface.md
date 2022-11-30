# App\OpenApiBundle\Api\DefaultApiInterface

All URIs are relative to *http://localhost:8000*

Method | HTTP request | Description
------------- | ------------- | -------------
[**calculerReponsesPost**](DefaultApiInterface.md#calculerReponsesPost) | **POST** /calculer/reponses | 
[**calculerScoresPost**](DefaultApiInterface.md#calculerScoresPost) | **POST** /calculer/scores | 


## Service Declaration
```yaml
# config/services.yaml
services:
    # ...
    Acme\MyBundle\Api\DefaultApi:
        tags:
            - { name: "open_api_server.api", api: "default" }
    # ...
```

## **calculerReponsesPost**
> calculerReponsesPost($reponsesACalculer)



### Example Implementation
```php
<?php
// src/Acme/MyBundle/Api/DefaultApiInterface.php

namespace Acme\MyBundle\Api;

use App\OpenApiBundle\Api\DefaultApiInterface;

class DefaultApi implements DefaultApiInterface
{

    // ...

    /**
     * Implementation of DefaultApiInterface#calculerReponsesPost
     */
    public function calculerReponsesPost(?ReponsesACalculer $reponsesACalculer, int &$responseCode, array &$responseHeaders): void
    {
        // Implement the operation ...
    }

    // ...
}
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **reponsesACalculer** | [**App\OpenApiBundle\Model\ReponsesACalculer**](../Model/ReponsesACalculer.md)|  | [optional]

### Return type

void (empty response body)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: Not defined

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

## **calculerScoresPost**
> calculerScoresPost($scoresACalculer)



### Example Implementation
```php
<?php
// src/Acme/MyBundle/Api/DefaultApiInterface.php

namespace Acme\MyBundle\Api;

use App\OpenApiBundle\Api\DefaultApiInterface;

class DefaultApi implements DefaultApiInterface
{

    // ...

    /**
     * Implementation of DefaultApiInterface#calculerScoresPost
     */
    public function calculerScoresPost(?ScoresACalculer $scoresACalculer, int &$responseCode, array &$responseHeaders): void
    {
        // Implement the operation ...
    }

    // ...
}
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **scoresACalculer** | [**App\OpenApiBundle\Model\ScoresACalculer**](../Model/ScoresACalculer.md)|  | [optional]

### Return type

void (empty response body)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: Not defined

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)


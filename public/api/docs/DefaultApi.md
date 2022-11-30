# ApiCortest.DefaultApi

All URIs are relative to *http://localhost:8000*

Method | HTTP request | Description
------------- | ------------- | -------------
[**calculerReponsesPost**](DefaultApi.md#calculerReponsesPost) | **POST** /calculer/reponses | 
[**calculerScoresPost**](DefaultApi.md#calculerScoresPost) | **POST** /calculer/scores | 



## calculerReponsesPost

> calculerReponsesPost(opts)



### Example

```javascript
import ApiCortest from 'api_cortest';

let apiInstance = new ApiCortest.DefaultApi();
let opts = {
  'reponsesACalculer': new ApiCortest.ReponsesACalculer() // ReponsesACalculer | 
};
apiInstance.calculerReponsesPost(opts, (error, data, response) => {
  if (error) {
    console.error(error);
  } else {
    console.log('API called successfully.');
  }
});
```

### Parameters


Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **reponsesACalculer** | [**ReponsesACalculer**](ReponsesACalculer.md)|  | [optional] 

### Return type

null (empty response body)

### Authorization

No authorization required

### HTTP request headers

- **Content-Type**: application/json
- **Accept**: Not defined


## calculerScoresPost

> calculerScoresPost(opts)



### Example

```javascript
import ApiCortest from 'api_cortest';

let apiInstance = new ApiCortest.DefaultApi();
let opts = {
  'scoresACalculer': new ApiCortest.ScoresACalculer() // ScoresACalculer | 
};
apiInstance.calculerScoresPost(opts, (error, data, response) => {
  if (error) {
    console.error(error);
  } else {
    console.log('API called successfully.');
  }
});
```

### Parameters


Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **scoresACalculer** | [**ScoresACalculer**](ScoresACalculer.md)|  | [optional] 

### Return type

null (empty response body)

### Authorization

No authorization required

### HTTP request headers

- **Content-Type**: application/json
- **Accept**: Not defined


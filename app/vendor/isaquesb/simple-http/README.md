# Http

Simple PHP Http Builder
 
### Requirements

- PHP >= 5.4

### Install

```
composer install
```

### Usage

```
$request = new \Simple\Http\Request\Request($adapter);
$response = $request->post('http://localhost', ['q' => 'Test POST']);
echo $response->getHttpStatus();
echo $response->getRawBody();
```

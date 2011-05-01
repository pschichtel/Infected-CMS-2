<pre><?php
    error_reporting(-1);

    require_once 'system/init.php';

    $target = 'http://www.google.de/';
    //$target = 'http://apptrackr.org/';
    //$target = 'http://netbeans.org/';
    $method = new GetRequestMethod();
    $http = new HttpClient();
    $http->setDebug(true);
    $http->setTarget($target);
    $http->setMethod($method);
    //$queryString = $http->generateQueryString(array());
    //$http->setRequestBody($queryString);
    //$http->addHeader(new HttpHeader('Connection', 'close'));

    $http->executeRequest();

    $response = $http->getResponseBody();
    $response = htmlspecialchars($response);
    echo '>' . $response . "<\n\n\n\n\n";


?></pre>

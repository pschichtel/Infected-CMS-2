<pre><?php
    error_reporting(-1);

    require_once 'system/init.php';

    $target = 'http://www.google.de/';
    //$target = 'http://apptrackr.org/';
    //$target = 'http://netbeans.org/';
    $http = new Http();
    $http->setDebug(true);
    

    $http->setTarget($target);
    $http->setMethod(new GetRequestMethod());
    $http->addHeader(new HttpHeader('Connection', 'close'));

    $http->executeRequest();

    //echo htmlspecialchars($http->getResponseBody());
    echo $http->getResponseBody();


?></pre>

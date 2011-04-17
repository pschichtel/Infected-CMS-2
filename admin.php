<pre><?php
    error_reporting(-1);

    require_once 'system/init.php';

    $target = 'http://www.google.de/';
    //$target = 'http://apptrackr.org/';
    //$target = 'http://netbeans.org/';
    $http = new Http();
    $http->setDebug(true);
    
    $target = 'http://localhost:6561/command/kick/';
    $http->setTarget($target);
    $http->setMethod(new PostRequestMethod());
    $queryString = $http->generateQueryString(array(
        'password' => 'changeMe',
        'params' => array(
            'quick_wango'
        )
    ));
    echo "QueryString: " . htmlspecialchars($queryString) . "\n";
    $http->setRequestBody($queryString);
    //$http->addHeader(new HttpHeader('Connection', 'close'));

    $http->executeRequest();

    //echo htmlspecialchars($http->getResponseBody());
    echo '>' . $http->getResponseBody() . '<';


?></pre>

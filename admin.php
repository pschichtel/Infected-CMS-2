<pre><?php
    $source = file_get_contents(__FILE__);
    $tokens = token_get_all($source);
    
    $source = htmlspecialchars($source);

    echo "<strong>Sourcecode:</strong> \n\n\n$source\n\n\n\n<strong>Tokens:</strong>\n\n\n";
    
    foreach ($tokens as $token)
    {
        $string = '';
        if (is_array($token) === true)
        {
            $string = token_name($token[0]);
        }
        else
        {
            $string = $token;
        }
        echo "{$string} ";
    }
?></pre>

<pre><?php

    include 'system/lib/text/bhm.php';
    include 'system/lib/debugging/debug.php';

    $string = 'supersicher';
    if (isset($_GET['string']) && trim($_GET['string']) !== '')
    {
        $string = rawurldecode($_GET['string']);
    }

    $salt = '';
    if (isset($_GET['salt']) && trim($_GET['salt']) !== '')
    {
        $salt = rawurldecode($_GET['salt']);
    }

    echo "Zeichenkette: $string\n";
    echo "Salt: $salt\n\n";
    $hasher = new bhm();
    $result = 'a';
    for ($i = 1; $i < 1001; $i++)
    {
        echo "Durchlauf(Hash-Length) $i:\n";
        $hasher->length = $i;
        $runtime = Debug::benchmark(array($hasher, 'bhm1'), array($string, $salt), $result);
        echo "    Hash 1: '{$hasher->hash}'\n    Laufzeit: $runtime\n";
        $runtime = Debug::benchmark(array($hasher, 'bhm1'), array($string, $salt), $result);
        echo "    Hash 2: '{$hasher->hash}'\n    Laufzeit: $runtime\n";
        $runtime = Debug::benchmark(array($hasher, 'bhm1'), array($string, $salt), $result);
        echo "    Hash 3: '{$hasher->hash}'\n    Laufzeit: $runtime\n";

        echo "\n\n------------------------------------\n\n\n";
    }


?></pre>

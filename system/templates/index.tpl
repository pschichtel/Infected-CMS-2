<html>
    <head>
{ForEach<data>}

<script>blubber{VARIABLE}</script>
{/ForEach}
        <title>{ViewHelper<head>:title}</title>
    </head>
    <body>
        <div id="maincontainer">
            <div id="colcontainer">
                <div id="">
                    <div id="content">
                        {SubTemplate<content>}
                    </div>
                </div>
            </div>
            {ForEach<test>}
                lol :{
                {If<cond>}
                    {ForEach<test2>}
                        rekursion :S
                    {/ForEach}
                {/If}
            {/ForEach}
        </div>
    </body>
</html>
<html>
    <head>
{Model<data>}

<script>blubber{VARIABLE}</script>
{/Model}
{Model<data>}

blubber
{VARIABLE}
{/Model}
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
            {Model<test>}
                lol :{
                {Model<test2>}
                    rekursion :S
                {/Model}
            {/Model}
        </div>
    </body>
</html>
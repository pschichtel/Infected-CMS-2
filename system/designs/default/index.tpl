{ViewHelper<head>:dtd}
<html>
    <head>
        <title>{ViewHelper<head>:title}</title>
        {ViewHelper<head>:style}
        {ViewHelper<head>:meta}
        {ViewHelper<head>:script}
    </head>
    <body>
        <div id="maincontainer">
            <div id="header">
                {SubTemplate<header>}
            </div>
            <div id="colcontainer">
                <div id="menu">
                    {ViewHelper<menu>:menu}
                </div>
                <div id="">
                    <div id="breadcrumb">
                        {ViewHelper<menu>:breadcrumb}
                    </div>
                    <div id="content">
                        {SubTemplate<content>}
                    </div>
                </div>
            </div>
            <div id="footer">
                {SubTemplate<footer>}
            </div>
            {Model<test>}
                lol :{

            {/Model}
        </div>
    </body>
</html>
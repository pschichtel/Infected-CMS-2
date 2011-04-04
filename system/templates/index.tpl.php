<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $title ?></title>
        <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    </head>
    <body>
        <div id="maincontainer">
            <div id="colcontainer">
                <div id="col_right">
                    <div id="content">
                        <?php $this->subTemplate('content') ?>
                    </div>
                </div>
                <div id="col_levt">
                    <div></div>
                </div>
            </div>
        </div>
    </body>
</html>
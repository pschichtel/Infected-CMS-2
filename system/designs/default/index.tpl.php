<?php echo $meta['dtd'] ?>
<html>
    <head>
        <title><?php echo $meta['title'] ?></title>
        <?php echo $meta['style'] ?>
        <?php echo $meta['metatags'] ?>
        <?php echo $meta['script'] ?>
    </head>
    <body>
        <div id="maincontainer">
            <div id="header">
                <?php $this->subTemplate('header') ?>
            </div>
            <div id="colcontainer">
                <div id="menu">
                    <?php $this->widget('menu') ?>
                </div>
                <div id="">
                    <div id="breadcrumb">
                        <?php $this->widget('breadcrumb') ?>
                    </div>
                    <div id="content">
                        <?php $this->subTemplate('content') ?>
                    </div>
                </div>
            </div>
            <div id="footer">
                <?php $this->subTemplate('footer') ?>
            </div>
        </div>
    </body>
</html>
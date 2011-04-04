<h1><?php echo $title ?></h1>
<div>
    <?php foreach ($data as $row): ?>
    <div>
        <div><?php echo $row['title'] ?></div>
        <div>
            <?php echo $row['text'] ?>
        </div>
        <div><?php echo $row['author'] ?></div>
    </div>
    <?php endif ?>
</div>
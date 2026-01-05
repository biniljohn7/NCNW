<?php
include 'lib/lib.php';
?>
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Add Query - DB Sync</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="assets/js/jquery-1.11.1.min.js"></script>
    <script src="assets/js/site-default.js"></script>
    <script src="assets/js/formchecker.js"></script>
    <script src="assets/js/add.js"></script>
</head>

<body>
    <?php
    $pix->getmsg();
    ?>

    <div class="mb50">
        <a href="<?php echo $pix->domain; ?>">
            &larr;
            Back to home
        </a>
    </div>

    <h1>Add Query</h1>

    <form id="form" action="actions/" method="post">
        <input type="hidden" name="method" value="query-add" />
        <div class="mb10">
            <strong>Your Name:</strong>
        </div>
        <div class="mb30">
            <input type="text" data-type="string" name="name" size="30" value="<?php echo have($_COOKIE['dbsyncuser']); ?>">
        </div>

        <div class="mb10">
            <strong>Query:</strong>
        </div>
        <div class="mb30">
            <textarea name="query" autofocus data-type="string" cols="100" rows="20" style="width: 100%;resize: vertical;"></textarea>
        </div>

        <button type="submit" class="pix-btn">
            SAVE QUERY
        </button>
    </form>


</body>

</html>
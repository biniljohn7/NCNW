<?php
include 'lib/lib.php';
?>
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>NCNW Sync DB</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="assets/js/jquery-1.11.1.min.js"></script>
    <script src="assets/js/site-default.js"></script>
    <script src="assets/js/home.js"></script>
</head>

<body>

    <h1>Sync Database</h1>

    <div class="query-list">
        <?php
        $dir = $pix->basedir . 'queries/';
        if (is_dir($dir)) {
            $dirData = array_slice(
                scandir($dir),
                2
            );
        } else {
            $dirData = array();
        }

        $markData = '';
        $markFile = $pix->getSyncMarkDir() . 'log.txt';
        if (is_file($markFile)) {
            $markData = file_get_contents($markFile);
        }

        foreach ($dirData as $sql) {
            $sqlKey = str_replace('.sql', '', $sql);
            if (
                stripos($markData, $sqlKey) === false
            ) {
        ?>
                <div class="qr-item" data-sql="<?php echo $sqlKey; ?>">
                    <div class="file-name">
                        <div class="name-info">
                            <?php echo $sql; ?>
                            <span class="status"></span>
                        </div>
                        <div class="ignore">
                            <span class="pix-btn round sm ignore-btn">
                                ignore
                            </span>
                        </div>
                    </div>
                    <div class="qr-txt"><?php echo file_get_contents($dir . $sql); ?></div>
                </div>
        <?php
            }
        }
        ?>
    </div>

    <div class="action-area">
        <span class="pix-btn lg start-sync" id="startSyncBtn">
            Start Sync
        </span>
        <a class="pix-btn add-qry-fx-btn" href="add.php">
            ADD
        </a>
    </div>

</body>

</html>
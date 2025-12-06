<?php
loadStyle('pages/sub-admins/data-import');
loadScript('pages/sub-admins/data-import');
?>
<h1>Sub Admins Import</h1>
<?php
breadcrumbs(
    [
        'Sub Admins',
        '?page=sub-admins'
    ],
    [
        'Import'
    ]
);

?>
<form action="" method="post" enctype="multipart/form-data" class="data-import-from mb50" id="importForm">
    <input type="hidden" name="method" value="sub-admin-import" />
    <div class="mb30">
        <input type="file" name="file" data-type="files" data-extensions="csv" id="csvFile" accept=".csv, text/csv" />
    </div>
    <div class="">
        <button type="submit" class="pix-btn site blod-600 mb20">
            Import Data
        </button>
    </div>
</form>
<div class="">
    <a href="<?php echo ADMINURL, 'files/sample/sub-admin-import-sample.csv'; ?>" download="Sub_Admins_Sample_File_Import.csv" class="sample-file">
        <span class="material-symbols-outlined">
            download
        </span>
        Download Sample CSV
    </a>
</div>
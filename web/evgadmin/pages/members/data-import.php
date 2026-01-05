<?php
loadStyle('pages/members/data-import');
loadScript('pages/members/data-import');
?>
<h1>Members Import</h1>
<?php
breadcrumbs(
    [
        'Members',
        '?page=members'
    ],
    [
        'Data Import'
    ]
);
?>
<form action="" method="post" enctype="multipart/form-data" class="data-import-from mb50" id="importForm">
    <input type="hidden" name="method" value="members-import" />
    <div class="mb30">
        <input type="file" name="file" data-type="files" data-extensions="csv" id="csvFile" accept=".csv, text/csv" />
    </div>
    <div class="">
        <button type="submit" class="pix-btn site blod-600 mb20">
            Import Data
        </button>
    </div>
    <div class="text-note">
        Duplicate Detection is enabled.
        When you import members, the system will automatically scan for existing records and notify you if a member already exists in Evergreen.
    </div>
</form>
<div class="">
    <a href="<?php echo ADMINURL, 'files/sample/members-import.csv'; ?>" download="Members_Sample_File_Import.csv" class="sample-file">
        <span class="material-symbols-outlined">
            download
        </span>
        Download Sample CSV
    </a>
</div>
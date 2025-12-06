<?php
(function ($pix, $pixdb, $evg) {
    $adData = false;
    if (isset($_GET['id'])) {
        $aid = esc($_GET['id']);
        if ($aid) {
            $adData = $pixdb->get(
                'advocacies',
                [
                    'id' => $aid,
                    'single' => 1
                ]
            );
        }
    }
    if (!$adData) {
        $pix->addmsg('Unknown advocacy');
        $pix->redirect('?page=advocacy');
    }

    if ($adData->locations) {
        $adData->locations = json_decode($adData->locations);
    }
    $membIds = [];
    $advMemb = $pixdb->get(
        'member_advocacy',
        [
            'advocacy' => $aid
        ],
        'member'
    );
    foreach ($advMemb->data as $row) {
        if ($row->member) {
            $membIds[] = $row->member;
        }
    }
    $memberDatas = $evg->getMembers($membIds, 'id,firstName,lastName,avatar');
    $advStatus = [
        'active' => 'Active',
        'inactive' => 'Inactive'
    ];

    loadStyle('pages/advocacy/details');
    loadScript('pages/advocacy/details');
?>
    <h1>
        Advocacy Details
    </h1>
    <?php
    breadcrumbs(
        [
            'Advocacies',
            '?page=advocacy'
        ],
        [
            $adData->title
        ]
    );
    ?>
    <div class="advocacy-wrap">
        <div class="wrap-top">
            <div class="wrap-content">
                <div class="cnt-hed">
                    <?php
                    echo $adData->title;
                    ?>
                </div>
                <div class="cnt-scope">
                    <div class="scope">
                        <div class="scope-lbl">
                            <?php
                            echo $evg->getBenefitScopeName($adData->scope);
                            ?>
                        </div>
                        <div class="scope-cnt">
                            <?php
                            $locations = $evg->getLocations(
                                $adData->scope,
                                $adData->locations,
                                'id, name'
                            );
                            foreach ($locations as $loc) {
                                echo $loc->name . '<br>';
                            }
                            ?>
                        </div>
                    </div>
                    <?php
                    if ($adData->enabled) {
                        $sts = $adData->enabled == 'Y' ? 'active' : 'inactive';
                    ?>
                        <div class="adv-sts">
                            <span class="sts <?php echo $sts; ?>">
                                <?php
                                echo $advStatus[$sts];
                                ?>
                            </span>
                        </div>
                    <?php
                    }
                    ?>
                </div>
                <div class="cnt-recip">
                    <div class="recip">
                        Recipient
                    </div>
                    <div class="rec-cnt">
                        <div class="itm">
                            <span class="material-symbols-outlined icn">
                                person_pin_circle
                            </span>
                            <?php
                            echo $adData->recipient;
                            ?>
                        </div>
                        <div class="itm">
                            <span class="material-symbols-outlined icn">
                                mail
                            </span>
                            <?php
                            echo $adData->recipEmail;
                            ?>
                        </div>
                    </div>
                </div>
                <?php
                if ($advMemb->data) {
                ?>
                    <div class="cnt-sign">
                        <div class="sign-label">
                            Signed By
                        </div>
                        <div class="sign-list">
                            <?php
                            foreach ($advMemb->data as $row) {
                                $firstName = $memberDatas[$row->member]->firstName ?? '--';
                                $lastName = $memberDatas[$row->member]->lastName ?? '--';
                            ?>
                                <a href="<?php echo $pix->adminURL, '?page=members&sec=details&id=', $row->member; ?>">
                                    <div class="sign-cnt">
                                        <div class="sign-thumb">
                                            <?php
                                            if (isset($memberDatas[$row->member]->avatar)) {
                                            ?>
                                                <img src="<?php echo $evg->getAvatar($memberDatas[$row->member]->avatar); ?>" alt="member image">
                                            <?php
                                            } else {
                                            ?>
                                                <span class="no-thumb">
                                                    <span class="material-symbols-outlined no-thmb">
                                                        person
                                                    </span>
                                                </span>
                                            <?php
                                            }
                                            ?>
                                        </div>
                                        <div class="sign-memb">
                                            <?php
                                            echo $firstName, ' ', $lastName;
                                            ?>
                                        </div>
                                    </div>
                                </a>
                            <?php
                            }
                            ?>
                        </div>
                    </div>
                <?php
                }
                ?>
                <div class="cnt-desc">
                    <div class="desc-lbl">
                        Description
                    </div>
                    <?php
                    echo nl2br($adData->descrptn);
                    ?>
                </div>
            </div>
            <div class="wrap-img" id="imgBox">
                <div class="img-prev" id="imgPrev">
                    <?php
                    if ($adData->image) {
                        $adData->image = $pix->uploadPath . 'advocacy-image/' . $pix->thumb(
                            $adData->image,
                            '450x450'
                        );
                    ?>
                        <img src="<?php echo $adData->image; ?>" alt="advocacy-image" class="adv-img">
                    <?php
                    } else {
                    ?>
                        <div class="no-img">
                            <span class="material-symbols-outlined img-icn">
                                wallpaper
                            </span>
                        </div>
                    <?php
                    }
                    ?>
                    <form action="<?php echo $pix->adminURL, 'actions/anyadmin/'; ?>" class="add-adv-img" method="post" enctype="multipart/form-data" id="advImgForm">
                        <input type="hidden" name="method" value="advocacy-image-upload" />
                        <input type="hidden" name="id" value="<?php echo $aid; ?>" />
                        <span class="material-symbols-outlined cam-icn">
                            photo_camera
                        </span>
                        <input type="file" name="photo" id="advImg" class="adv-img-inp" />
                    </form>
                </div>
            </div>
        </div>
        <div class="wrap-btm">
            <div class="cnt-pdf">
                <div class="pdf-lbl">
                    PDF
                </div>
                <?php
                echo htmlspecialchars_decode($adData->pdfContent, ENT_QUOTES);
                ?>
            </div>
            <div class="cnt-btns">
                <a href="<?php echo $pix->adminURL . "?page=advocacy&sec=mod&id=$adData->id"; ?>">
                    <span class="btn edt">
                        <span class="material-symbols-outlined icn">
                            edit
                        </span>
                        <span>
                            Edit
                        </span>
                    </span>
                </a>
                <a href="<?php echo $pix->adminURL . "actions/anyadmin/?method=advocacy-delete&id=$adData->id"; ?>">
                    <span class="btn dlt confirm">
                        <span class="material-symbols-outlined icn">
                            delete
                        </span>
                        <span>
                            Delete
                        </span>
                    </span>
                </a>
                <?php
                if($advMemb->data) {
                ?>
                    <a 
                        href="<?php echo ADMINURL . "actions/anyadmin/?method=petition-export&id=" . $aid; ?>" 
                        class="pix-btn sm site" 
                        target="_blank"
                    >
                        <span class="material-symbols-outlined icn">
                            upgrade
                        </span>
                        <span>
                            Export
                        </span>
                    </a>
                <?php
                }
                ?>
            </div>
        </div>
    </div>
<?php
})($pix, $pixdb, $evg);
?>
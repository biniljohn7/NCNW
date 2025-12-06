<?php 
(function($pix, $pixdb, $evg){
    $msId = esc($_GET['id'] ?? 'new');
    $mId = esc($_GET['mid'] ?? '');
    $new = $msId == 'new';

    if(!$new) {
        $validMembership = false;
        if($msId) {
            $membership = $pixdb->getRow(
                'memberships',
                ['id' => $msId]
            );
            $validMembership = !!$membership;
        }

        if(!$validMembership) {
            $pix->addmsg("Unknown membership");
            $pix->redirect("?page=members&sec=details&id=$mId");
        }
    }

    $member = $pixdb->getRow(
        'members',
        ['id' => $mId]
    );

    $membershipPlans = $pixdb->get(
        'membership_plans',
        ['#SRT' => 'title asc'],
        'id, title'
    );

    $payCategories = $pixdb->get(
        'products',
        ['#SRT' => 'name asc'],
        'id, name'
    );

    if($mId) {
        $selCategories = array_flip(
            $pixdb->getCol(
                'paid_benefits',
                ['members' => $mId],
                'payCategoryId'
            )
        );
    }

    loadScript('pages/members/mod-membership');
    loadStyle('pages/members/mod');
?>
<h1>
    <?php
    echo $new ? 'Add' : 'Modify'
    ?>
    Membership
</h1>

<?php
    breadcrumbs(
        [
            'Members',
            '?page=members'
        ],
        !$new ? [
            $member->firstName . ' ' . $member->lastName,
            '?page=members&sec=details&id=' . $member->id
        ] : null,
        [
            ($new ? 'Add' : 'Modify') . ' Membership'
        ]
    );
?>

<form action="<?php echo ADMINURL, 'actions/anyadmin/'; ?>" method="post" id="membershipForm">
    <input type="hidden" name="method" value="membership-save">
    <input type="hidden" name="mid" value="<?php echo $mId; ?>">
    <div class="fm-field">
        <div class="fld-label">
            Memberhsip
        </div>
        <div class="fld-inp">
            <select name="membership" data-type="string">
                <option value="" disabled selected>Choose Membership Plan</option>
                <?php
                $selMembership = $new ? '' : ($membership->planId ?? '');
                foreach($membershipPlans->data as $row) {
                    echo '<option ', $selMembership == $row->id ? 'selected' : '' ,' value="', $row->id ,'">', $row->title ,'</option>';
                }
                ?>
            </select>
        </div>
    </div>

    <!-- <div class="fm-field">
        <div class="fld-label">
            Payment Categories
        </div>
        <div class="fld-inp">
        <?php
        // foreach($payCategories->data as $row) {
        //     $checked = $selCategories && isset($selCategories[$row->id]);
        //     CheckBox(
        //         $row->name,
        //         'categories',
        //         $row->id,
        //         $checked
        //     );
        //     echo '<br />';
        // }
        ?>
        </div>
    </div> -->

    <div class="fm-field">
        <div class="fld-inp">
            <input type="submit" class="pix-btn lg site bold-500" value="<?php echo $new ? 'Save' : 'Update'; ?>">
        </div>
    </div>
</form>
<?php
})($pix, $pixdb, $evg);
?>
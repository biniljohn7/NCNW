<?php
(function ($pix, $pixdb, $evg) {
    $member = false;
    if (isset($_GET['id'])) {
        $id = esc($_GET['id']);
        if ($id) {
            $member = $pixdb->getRow(
                'members',
                ['id' => $id]
            );
        }
    }
    if (!$member) {
        $pix->addmsg('Invalid member.');
        $pix->redirect('?page=members');
    }

    $info = $pixdb->getRow('members_info', ['member' => $id]);
    if (!$info) {
        $info = new stdClass();
    }

    loadStyle('pages/members/mod');

?>

    <h1>Members</h1>
    <?php
    breadcrumbs(
        ['Members', '?page=members'],
        [$member->firstName . ' ' . $member->lastName, '?page=members&sec=details&id=' . $member->id],
        ['Edit']
    );
    ?>

    <form>
        <div class="fm-field">
            <div class="fld-label">
                Status Update
            </div>
            <div class="fld-inp">
                <textarea cols="70" rows="4" placeholder="Status" id="statusUpdate">test 123</textarea>
            </div>
        </div>

        <div class="fm-field">
            <div class="fld-label">Prefix</div>
            <div class="fld-inp">
                <select>
                    <option>mr</option>
                </select>
            </div>
        </div>

        <div class="fm-field">
            <div class="fld-label">First Name</div>
            <div class="fld-inp">
                <input type="text" placeholder="First Name" id="firstName" class="" value="Albert" size="35">
            </div>
        </div>

        <div class="fm-field">
            <div class="fld-label">Last Name</div>
            <div class="fld-inp">
                <input type="text" placeholder="Last Name" id="lastName" class="" value="John" size="35">
            </div>
        </div>

        <div class="fm-field">
            <div class="fld-label">Suffix</div>
            <div class="fld-inp">
                <select>
                    <option>Sr</option>
                </select>
            </div>
        </div>

        <div class="fm-field">
            <div class="fld-label">Country</div>
            <div class="fld-inp">
                <select>
                    <option>USA</option>
                </select>
            </div>
        </div>

        <div class="fm-field">
            <div class="fld-label">State</div>
            <div class="fld-inp">
                <select>
                    <option>New York</option>
                </select>
            </div>
        </div>

        <div class="fm-field">
            <div class="fld-label">City</div>
            <div class="fld-inp">
                <input type="text" placeholder="City" id="city" class="" value="NY" size="35">
            </div>
        </div>

        <div class="fm-field">
            <div class="fld-label">Street Address</div>
            <div class="fld-inp">
                <input type="text" placeholder="Street Address" id="address" class="" value="NY addr" size="35">
            </div>
        </div>

        <div class="fm-field">
            <div class="fld-label">Zip</div>
            <div class="fld-inp">
                <input type="text" placeholder="Zip" id="zip" class="" value="123456" size="35">
            </div>
        </div>

        <div class="fm-field ">
            <div class="fld-label">Phone Number</div>
            <div class="fld-inp">
                <input type="text" placeholder="Phone Number" id="phoneNumber" class="" value="9744004042" size="35">
            </div>
        </div>

        <div class="fm-field">
            <div class="fld-label">Email</div>
            <div class="fld-inp">
                <input type="text" placeholder="Email" disabled="" value="albert@octopix.net" size="35">
            </div>
        </div>

        <div class="fm-field">
            <div class="fld-label">Member ID</div>
            <div class="fld-inp">
                <input type="text" placeholder="Member ID" disabled="" value="123321" size="35">
            </div>
        </div>

        <div class="fm-field">
            <div class="fld-label">Biography</div>
            <div class="fld-inp">
                <textarea cols="70" rows="6" placeholder="Biography" id="biography">qwerty</textarea>
            </div>
        </div>

        <div class="fm-field">
            <div class="fld-label">Occupation</div>
            <div class="fld-inp">
                <select>
                    <option>Account Collector</option>
                </select>
            </div>
        </div>

        <div class="fm-field">
            <div class="fld-label">Industry</div>
            <div class="fld-inp">
                <select>
                    <option>Accounting</option>
                </select>
            </div>
        </div>

        <div class="fm-field">
            <div class="fld-label">Education</div>
        </div>

        <div class="fm-field">
            <div class="fld-label">Certification</div>
            <div class="fld-inp">
                checkbox
            </div>
        </div>

        <div class="fm-field">
            <div class="fld-label">Leadership Role</div>
            <div class="fld-inp">
                <input type="text" placeholder="Leadership Role" id="leadershipRole" value="fffffff" size="35">
            </div>
        </div>

        <div class="fm-field">
            <div class="fld-label">Household</div>
            <div class="fld-inp">
                <select>
                    <option>Married</option>
                </select>
            </div>
        </div>

        <div class="fm-field">
            <div class="fld-label">Salary Range</div>
            <div class="fld-inp">
                <select>
                    <option>$40,001 to $80,000</option>
                </select>
            </div>
        </div>

        <div class="fm-field">
            <div class="fld-label">Expertise</div>
            <div class="fld-inp">
                checkbox
            </div>
        </div>

        <div class="fm-field mb30">
            <div class="fld-label">Affiliate Organization</div>
            <div class="fld-inp">
                <input type="text" placeholder="Affiliate Organization" id="affilateOrgzn" value="" size="35">
            </div>
        </div>

        <div class="mb30">
            Organizational Data
        </div>

        <div class="fm-field">
            <div class="fld-label">Nation</div>
            <div class="fld-inp">
                <select>
                    <option>USA</option>
                </select>
            </div>
        </div>

        <div class="fm-field">
            <div class="fld-label">Region</div>
            <div class="fld-inp">
                <select>
                    <option>Southeast</option>
                </select>
            </div>
        </div>

        <div class="fm-field">
            <div class="fld-label">Organizational State</div>
            <div class="fld-inp">
                <select>
                    <option>Florida</option>
                </select>
            </div>
        </div>

        <div class="fm-field position-relative">
            <div class="fld-label">Section of Initiation</div>
            <div class="fld-inp">
                <select>
                    <option>Columbus Ohio Section</option>
                </select>
            </div>
        </div>

        <div class="fm-field">
            <div class="fld-label">Year of Initiation</div>
            <div class="fld-inp">
                <input type="text" id="yearOfIni" value="2020-02-11" size="35">
            </div>
        </div>

        <div class="fm-field">
            <div class="fld-label">Current Section</div>
            <div class="fld-inp">
                <select>
                    <option>St. Petersburg Metropolitan Section</option>
                </select>
            </div>
        </div>
        <div class="text-center">
            <button type="button" class="pix-btn lg site bold-500">SAVE</button>
        </div>
    </form>

<?php

})($pix, $pixdb, $evg);

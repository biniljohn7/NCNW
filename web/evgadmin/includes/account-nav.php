<?php
if ($lgUser) {
?>
    <div class="acc-sidebar" id="mainSideBar">
        <div class="menu-head" id="mobMenuCloseBtn">
            <span class="material-symbols-outlined">
                keyboard_backspace
            </span>
            <span class="mh-text">
                Navigation
            </span>
        </div>
        <div class="sd-top">
            <div class="user-thumb ncnw">
                <img src="<?php echo ADMINURL; ?>assets/images/ncnw-logo-new.png">
            </div>
            <!-- <div class="user-info">
                <div class="user-name">
                    NCNW
                </div>
            </div> -->
        </div>
        <div class="sd-menus" id="sideMenuBody">
            <?php
            foreach ($menus as $menu) {
                if ($menu) {
                    $menu['items'] = array_filter($menu['items']);
                    if (!empty($menu['items'])) {
            ?>
                        <div class="menu-gp">
                            <div class="gp-name">
                                <?php echo $menu['name']; ?>
                            </div>
                            <div class="gp-items">
                                <?php
                                foreach ($menu['items'] as $mi) {
                                    if ($mi) {
                                        $matchArgs = $mi[3] ?? false;
                                        $urlMatch = false;

                                        if (
                                            $matchArgs &&
                                            preg_match('/^re\//i', $matchArgs)
                                        ) {
                                            $pattern = substr($matchArgs, 2);
                                            $urlMatch = !!preg_match($pattern, $pix->reqURI);
                                            // 
                                        } else {
                                            $urlMatch = stripos($pix->reqURI, $mi[0]);
                                            if ($urlMatch !== false && $matchArgs) {
                                                if (
                                                    strpos(
                                                        substr($pix->reqURI, $urlMatch + strlen($mi[0])),
                                                        '/'
                                                    ) !== false
                                                ) {
                                                    $urlMatch = false;
                                                }
                                            }
                                        }
                                ?>
                                        <a href="<?php echo ADMINURL, $mi[0]; ?>" class="menu-item <?php echo $urlMatch !== false ? 'active' : ''; ?>">
                                            <span class="icon material-symbols-outlined">
                                                <?php echo $mi[1]; ?>
                                            </span>
                                            <?php echo $mi[2]; ?>
                                        </a>
                                <?php
                                    }
                                }
                                ?>
                            </div>
                        </div>
            <?php
                    }
                }
            }
            ?>
        </div>
        <div class="sd-logout">
            <a href="<?php echo ADMINURL, 'actions/public/?method=logout'; ?>">
                <span class="material-symbols-outlined">
                    logout
                </span>
                Logout
            </a>
        </div>
    </div>
<?php
}
?>
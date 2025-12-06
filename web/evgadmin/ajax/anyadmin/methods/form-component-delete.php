<?php
(function (&$r, $pix) {

    $_ = $_REQUEST;
    $delete = false;
    if (isset($_['label']) && isset($_['type'])) {
        $label = esc($_['label']);
        $type = esc($_['type']);

        if ($label && $type) {
            $components = $pix->getData('form/components', false);
            if (!is_array($components)) {
                $components = [];
            }
            foreach ($components as $key => $itm) {
                if ($itm->label == $label && $itm->type == $type) {
                    $delete = true;
                    unset($components[$key]);
                    break;
                }
            }
            if($delete){
                $pix->setData('form/components', $components, true);
            }
        }
    }
    if($delete){
        $r->status = 'ok';
        $r->message = 'Field deleted successfully.';
    }
})($r, $pix);

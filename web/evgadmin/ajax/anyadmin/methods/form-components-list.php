<?php
(function (&$r, $pix) {
    $r->status = 'ok';
    $components = $pix->getData('form/components', false);
    if (!is_array($components)) {
        $components = [];
    }
    $r->list  = $components;
})($r, $pix);

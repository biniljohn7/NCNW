<?php
(function (&$r, $pix) {

    $_ = $_REQUEST;
    $jsData = null;
    if (isset($_['label']) && isset($_['type'])) {
        $label = esc($_['label']);
        $type = esc($_['type']);
        $options = isset($_['options']) ? esc($_['options']) : [];

        if ($label && $type) {
            if ($type == 'text') {
                $jsData = [
                    'label' => $label,
                    'type' => $type
                ];
            } elseif ($type == 'select') {
                $options = array_unique(array_filter(explode(',', $options)));
                if ($options) {
                    $jsData = [
                        'label' => $label,
                        'type' => $type,
                        'options' => $options
                    ];
                }
            }
        }
    }
    if ($jsData) {
        $jsData = (object)$jsData;
        $components = $pix->getData('form/components', false);
        if (!is_array($components)) {
            $components = [];
        }
        $exist = false;
        foreach ($components as $key => $itm) {
            if ($itm->label == $jsData->label && $itm->type == $jsData->type) {
                $exist = true;
                $components[$key] = $jsData;
                break;
            }
        }

        if (!$exist) {
            $components[] = $jsData;
        }
        $pix->setData('form/components', $components, true);

        $r->status = 'ok';
        $r->data = $jsData;
    } else {
        $r->status = 'error';
        $r->message = 'Invalid request.';
    }
})($r, $pix);

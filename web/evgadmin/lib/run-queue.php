<?php
$trgtFile = current($trgtFile);
$keyFile = $qDir . $trgtFile;

if (is_file($keyFile)) {
    ////  move file to working directory
    if (!is_dir($qDir . 'working/')) {
        mkdir($qDir . 'working/', 0755, true);
    }

    $destination = $qDir . 'working/' . pathinfo($trgtFile, PATHINFO_FILENAME) . '-' . (time() + 900) . '.txt';
    copy($keyFile, $destination);
    unlink($keyFile);

    ///  read file data
    $fData = file_get_contents($destination);
    $fData = unserialize($fData);

    if (
        isset(
            $fData->func,
            $fData->args
        ) &&
        (
            is_array($fData->func) ||
            is_string($fData->func)
        ) &&
        is_array($fData->args)
    ) {
        ///   call user function
        $res = call_user_func_array(
            is_array($fData->func) ?
                [
                    $GLOBALS[$fData->func[0]],
                    $fData->func[1]
                ] :
                $fData->func,
            $fData->args
        );

        //unlink($destination);
    }

    unlink($destination);
}

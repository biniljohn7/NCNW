<?php
if(isset(
	$_['name'],
	$_['query']
)){
	$name = esc($_['name']);
	$query = $_['query'];

	if(
		$name &&
		$query
	){
        $qryDir = $pix->basedir.'queries/';
        if(!is_dir($qryDir)){
            mkdir($qryDir, 0755, true);
        }
        $qkey = date('Y-m-d-His').'-'.$pix->makestring(8, 'ln');
        $qfile = $qryDir.$qkey.'.sql';

        file_put_contents(
            $qfile,
            '-- '.$name.' - '.date('d F Y - h:i A')."\n\n\n".$query
        );

        $pix->markSync($qkey);

        setcookie('dbsyncuser', $name, time()+31536000, '/');// 1 year expiry

        $pix->addmsg('Query added !', 1);
	}
}
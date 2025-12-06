<?php
include_once '../lib/lib.php';

$db->query('UPDATE nations n
JOIN (
    SELECT 
        country AS nation_id,
        COUNT(*) AS total_members
    FROM 
        members_info
    WHERE 
        country IS NOT NULL
    GROUP BY 
        country
) m ON n.id = m.nation_id
SET n.members = m.total_members;
');

$db->query('UPDATE states n
JOIN (
    SELECT 
        state AS state_id,
        COUNT(*) AS total_members
    FROM 
        members_info
    WHERE 
        state IS NOT NULL
    GROUP BY 
        state
) m ON n.id = m.state_id
SET n.members = m.total_members;
');

$db->query('UPDATE regions n
JOIN (
    SELECT 
        region AS region_id,
        SUM(members) AS total_members
    FROM 
        states
    WHERE 
        region IS NOT NULL
    GROUP BY 
        region
) m ON n.id = m.region_id
SET n.members = m.total_members;
');


$db->query('UPDATE chapters n
JOIN (
    SELECT 
        cruntChptr AS chapter_id,
        COUNT(*) AS total_members
    FROM 
        members_info
    WHERE 
        cruntChptr IS NOT NULL
    GROUP BY 
        cruntChptr
) m ON n.id = m.chapter_id
SET n.members = m.total_members;
');


<?php
$duplicates = $pixdb->fetchAll("SELECT 'email' AS match_type, m.email AS match_value,
       GROUP_CONCAT(m.id) AS member_ids, COUNT(*) AS cnt
        FROM members m
        WHERE m.email <> ''
        GROUP BY m.email
        HAVING cnt > 1

        UNION ALL

        SELECT 'phone', mi.phone, GROUP_CONCAT(mi.member), COUNT(*)
        FROM members_info mi
        WHERE mi.phone <> ''
        GROUP BY mi.phone
        HAVING COUNT(*) > 1

        UNION ALL

        SELECT 'address', mi.address, GROUP_CONCAT(mi.member), COUNT(*)
        FROM members_info mi
        WHERE mi.address <> ''
        GROUP BY mi.address
        HAVING COUNT(*) > 1

        UNION ALL

        SELECT 'name', CONCAT_WS(' ', m.firstName, m.middleName, m.lastName),
            GROUP_CONCAT(m.id), COUNT(*)
        FROM members m
        GROUP BY CONCAT_WS(' ', m.firstName, m.middleName, m.lastName)
        HAVING COUNT(*) > 1;
");
?>
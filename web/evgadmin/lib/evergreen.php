<?php
class EverGreen
{
    public $evgDir = null;
    public const benefitScopeNames = [
        'national' => 'National',
        'state' => 'State',
        'regional' => 'Regional',
        'chapter' => 'Section'
    ];
    public const benefitStatusNames = [
        'active' => 'Active',
        'inactive' => 'Inactive'
    ];

    public const scopeTables = [
        'national' => 'nations',
        'regional' => 'regions',
        'state' => 'states',
        'chapter' => 'chapters'
    ];

    public const reqTypes = [
        'change' => 'Change Request',
        'bug' => 'System Error/Bug'
    ];
    public const priorities = [
        'low' => 'Low',
        'medium' => 'Medium',
        'high' => 'High'
    ];

    public const tables = [
        'officers_titles' => 'Titles',
        'states' => 'States',
        'chapters' => 'Sections',
        'regions' => 'Regions',
        'affiliates' => 'Affiliates'
    ];

    public const permissions = [
        'section-president' => 'Section President',
        'section-leader' => 'Section Leader',
        'state-leader' => 'State Leader',
        'officer-president' => 'Officer President',
        'first-vice-president' => 'First Vice President',
        'second-vice-president' => 'Second Vice President',
        'treasurer' => 'Treasurer',
        'collegiate-liaison' => 'Collegiate Liaison'
    ];

    public const prefix = [
        2933 => "Mr.",
        2934 => "Mrs.",
        2935 => "Dr.",
        2936 => "Ms.",
        3475 => "Ambassador",
        3476 => "Atty.",
        3477 => "Bishop",
        3478 => "Commissioner",
        3479 => "Elder",
        3480 => "Judge",
        3481 => "Mayor",
        3482 => "Min.",
        3483 => "Miss",
        3484 => "Pastor",
        3485 => "President",
        3486 => "Representative",
        3487 => "Rev.",
        3488 => "Rev. Dr.",
        3489 => "Senator",
        3490 => "The Honorable",
    ];

    public const suffix = [
        2937 => "Jr.",
        2938 => "Sr.",
        2939 => "II",
        2940 => "III",
        3474 => "IV",
    ];

    public const houseHold = [
        2971 => "Single",
        2972 => "Single with children",
        2973 => "Married",
        2974 => "Married with children",
        3469 => "Widowed",
        3470 => "Widowed with children",
        3495 => "Never Married",
        3496 => "Separated",
        3497 => "Divorced",
    ];

    public const employmentStatus = [
        3506 => "Employed full-time",
        3507 => "Employed part-time",
        3508 => "Self-employed",
        3509 => "Unemployed"
    ];

    public const volunteerInterest = [
        3510 => "Health Equity",
        3511 => "Education",
        3512 => "Social Justice",
        3513 => "Economic Empowerment",
        3514 => "Mentorship"
    ];

    public const racialIdentity = [
        3498 => "American Indian or Alaska Native",
        3499 => "Asian",
        3500 => "Black or African American",
        3501 => "Hispanic, Latino, or Spanish Origin",
        3502 => "Middle Eastern or North African",
        3503 => "Native Hawaiian or Other Pacific Islander",
        3504 => "White"
        // null => "Other (please specify)"
    ];

    public const salaryRange = [
        2966 => "No income",
        2967 => "0 - $40,000",
        2968 => "$40,001 to $80,000",
        2969 => "$80,001 to $120,000",
        2970 => "$120,001 to $160,000",
        3471 => "$160,001 to $200,000",
        3472 => "$200,000 to $240,000",
        3473 => "$240,000+",
    ];

    public const expertise = [
        2959 => "Analytical",
        2960 => "Communication",
        2961 => "Computer",
        2962 => "Conceptual",
        2963 => "Core Competencies",
        2964 => "Creative Thinking",
        2965 => "Critical Thinking",
    ];

    public const degree = [
        2941 => "Associate's degree",
        2942 => "Bachelor's degree",
        2943 => "Master's degree",
        2944 => "Doctoral Degree",
        2945 => "Professional degree",
        3492 => "Less than high school",
        3493 => "High school diploma or equivalent",
        3494 => "Some college"
    ];

    public const memberRole = [
        'state-leader' => 'State Leader',
        'section-leader' => 'Section Leader',
        'section-president' => 'Section President',
        'affiliate-leader' => 'Affiliate Leader',
        'collegiate-leaders' => 'Collegiate Leaders'
    ];

    public $states = [
        'AL' => [
            'nation' => 'USA',
            'region' => 'Southeast',
            'name' => 'Alabama',
        ],
        'AK' => [
            'nation' => 'USA',
            'region' => 'West',
            'name' => 'Alaska',
        ],
        'AS' => [
            'nation' => 'USA',
            'region' => 'West',
            'name' => 'American Samoa',
        ],
        'AZ' => [
            'nation' => 'USA',
            'region' => 'West',
            'name' => 'Arizona',
        ],
        'AR' => [
            'nation' => 'USA',
            'region' => 'Southeast',
            'name' => 'Arkansas',
        ],
        'CA' => [
            'nation' => 'USA',
            'region' => 'West',
            'name' => 'California',
        ],
        'CO' => [
            'nation' => 'USA',
            'region' => 'West',
            'name' => 'Colorado',
        ],
        'CT' => [
            'nation' => 'USA',
            'region' => 'Northeast',
            'name' => 'Connecticut',
        ],
        'DC' => [
            'nation' => 'USA',
            'region' => 'Northeast',
            'name' => 'District of Columbia',
        ],
        'DE' => [
            'nation' => 'USA',
            'region' => 'Northeast',
            'name' => 'Delaware',
        ],
        'FL' => [
            'nation' => 'USA',
            'region' => 'Southeast',
            'name' => 'Florida',
        ],
        'GA' => [
            'nation' => 'USA',
            'region' => 'Southeast',
            'name' => 'Georgia',
        ],
        'GU' => [
            'nation' => 'USA',
            'region' => 'West',
            'name' => 'Guam',
        ],
        'HI' => [
            'nation' => 'USA',
            'region' => 'West',
            'name' => 'Hawaii',
        ],
        'IA' => [
            'nation' => 'USA',
            'region' => 'Midwest',
            'name' => 'Iowa',
        ],
        'ID' => [
            'nation' => 'USA',
            'region' => 'West',
            'name' => 'Idaho',
        ],
        'IL' => [
            'nation' => 'USA',
            'region' => 'Midwest',
            'name' => 'Illinois',
        ],
        'IN' => [
            'nation' => 'USA',
            'region' => 'Midwest',
            'name' => 'Indiana',
        ],
        'KS' => [
            'nation' => 'USA',
            'region' => 'Midwest',
            'name' => 'Kansas',
        ],
        'KY' => [
            'nation' => 'USA',
            'region' => 'Southeast',
            'name' => 'Kentucky',
        ],
        'LA' => [
            'nation' => 'USA',
            'region' => 'Southwest',
            'name' => 'Louisiana',
        ],
        'ME' => [
            'nation' => 'USA',
            'region' => 'Northeast',
            'name' => 'Maine',
        ],
        'MD' => [
            'nation' => 'USA',
            'region' => 'Northeast',
            'name' => 'Maryland',
        ],
        'MA' => [
            'nation' => 'USA',
            'region' => 'Northeast',
            'name' => 'Massachusetts',
        ],
        'MI' => [
            'nation' => 'USA',
            'region' => 'Midwest',
            'name' => 'Michigan',
        ],
        'MN' => [
            'nation' => 'USA',
            'region' => 'Midwest',
            'name' => 'Minnesota',
        ],
        'MS' => [
            'nation' => 'USA',
            'region' => 'Southeast',
            'name' => 'Mississippi',
        ],
        'MO' => [
            'nation' => 'USA',
            'region' => 'Midwest',
            'name' => 'Missouri',
        ],
        'MT' => [
            'nation' => 'USA',
            'region' => 'West',
            'name' => 'Montana',
        ],
        'NE' => [
            'nation' => 'USA',
            'region' => 'Midwest',
            'name' => 'Nebraska',
        ],
        'NV' => [
            'nation' => 'USA',
            'region' => 'West',
            'name' => 'Nevada',
        ],
        'NH' => [
            'nation' => 'USA',
            'region' => 'Northeast',
            'name' => 'New Hampshire',
        ],
        'NJ' => [
            'nation' => 'USA',
            'region' => 'Northeast',
            'name' => 'New Jersey',
        ],
        'NM' => [
            'nation' => 'USA',
            'region' => 'Southwest',
            'name' => 'New Mexico',
        ],
        'NY' => [
            'nation' => 'USA',
            'region' => 'Northeast',
            'name' => 'New York',
        ],
        'NC' => [
            'nation' => 'USA',
            'region' => 'Southeast',
            'name' => 'North Carolina',
        ],
        'ND' => [
            'nation' => 'USA',
            'region' => 'Midwest',
            'name' => 'North Dakota',
        ],
        'OH' => [
            'nation' => 'USA',
            'region' => 'Midwest',
            'name' => 'Ohio',
        ],
        'OK' => [
            'nation' => 'USA',
            'region' => 'Southwest',
            'name' => 'Oklahoma',
        ],
        'OR' => [
            'nation' => 'USA',
            'region' => 'West',
            'name' => 'Oregon',
        ],
        'PA' => [
            'nation' => 'USA',
            'region' => 'Northeast',
            'name' => 'Pennsylvania',
        ],
        'PR' => [
            'nation' => 'USA',
            'region' => 'Northeast',
            'name' => 'Puerto Rico',
        ],
        'RI' => [
            'nation' => 'USA',
            'region' => 'Northeast',
            'name' => 'Rhode Island',
        ],
        'SC' => [
            'nation' => 'USA',
            'region' => 'Southeast',
            'name' => 'South Carolina',
        ],
        'SD' => [
            'nation' => 'USA',
            'region' => 'Midwest',
            'name' => 'South Dakota',
        ],
        'TN' => [
            'nation' => 'USA',
            'region' => 'Southeast',
            'name' => 'Tennessee',
        ],
        'TX' => [
            'nation' => 'USA',
            'region' => 'Southwest',
            'name' => 'Texas',
        ],
        'UT' => [
            'nation' => 'USA',
            'region' => 'West',
            'name' => 'Utah',
        ],
        'VT' => [
            'nation' => 'USA',
            'region' => 'Northeast',
            'name' => 'Vermont',
        ],
        'VA' => [
            'nation' => 'USA',
            'region' => 'Southeast',
            'name' => 'Virginia',
        ],
        'WA' => [
            'nation' => 'USA',
            'region' => 'West',
            'name' => 'Washington',
        ],
        'WV' => [
            'nation' => 'USA',
            'region' => 'Southeast',
            'name' => 'West Virginia',
        ],
        'WI' => [
            'nation' => 'USA',
            'region' => 'Midwest',
            'name' => 'Wisconsin',
        ],
        'WY' => [
            'nation' => 'USA',
            'region' => 'West',
            'name' => 'Wyoming',
        ],
        'MP' => [
            'nation' => 'USA',
            'region' => 'West',
            'name' => 'Northern Mariana Islands',
        ],
        'VI' => [
            'nation' => 'USA',
            'region' => 'Northeast',
            'name' => 'U.S. Virgin Islands',
        ],
    ];

    public $sections = array(
        'athens-westmont-section' =>
        array(
            'state' => 'Georgia',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Athens Westmont Section',
        ),
        'northwest-georgia-section' =>
        array(
            'state' => 'Georgia',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Northwest Georgia Section',
        ),
        'broward-county-section' =>
        array(
            'state' => 'Florida',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Broward County Section',
        ),
        'charles-county-section' =>
        array(
            'state' => 'Maryland',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Charles County Section',
        ),
        'brooklyn-section' =>
        array(
            'state' => 'New York',
            'region' => 'Northeast',
            'nation' => 'USA',
            'name' => 'Brooklyn Section',
        ),
        'dallas-metropolitan-section' =>
        array(
            'state' => 'Texas',
            'region' => 'Southwest',
            'nation' => 'USA',
            'name' => 'Dallas Metropolitan Section',
        ),
        'santa-clara-county-section' =>
        array(
            'state' => 'California',
            'region' => 'West',
            'nation' => 'USA',
            'name' => 'Santa Clara County Section',
        ),
        'winston-salem-section' =>
        array(
            'state' => 'North Carolina',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Winston-Salem Section',
        ),
        'prince-george-s-county-section' =>
        array(
            'state' => 'Maryland',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => "Prince George's County Section",
        ),
        'delaware-valley-pa-section' =>
        array(
            'state' => 'Pennsylvania',
            'region' => 'Northeast',
            'nation' => 'USA',
            'name' => 'Delaware Valley PA Section',
        ),
        'greater-baltimore-section' =>
        array(
            'state' => 'Maryland',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Greater Baltimore Section',
        ),
        'newport-news-hampton-section' =>
        array(
            'state' => 'Virginia',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Newport News-Hampton Section',
        ),
        'mitchellville-bowie-section' =>
        array(
            'state' => 'Maryland',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Mitchellville/Bowie Section',
        ),
        'washington-section' =>
        array(
            'state' => 'District of Columbia',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Washington Section',
        ),
        'queens-county-section' =>
        array(
            'state' => 'New York',
            'region' => 'Northeast',
            'nation' => 'USA',
            'name' => 'Queens County Section',
        ),
        'tampa-metropolitan-section' =>
        array(
            'state' => 'Florida',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Tampa Metropolitan Section',
        ),
        'tupelo-lee-county-section' =>
        array(
            'state' => 'Mississippi',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Tupelo Lee County Section',
        ),
        'greater-boston-section' =>
        array(
            'state' => 'Massachusetts',
            'region' => 'Northeast',
            'nation' => 'USA',
            'name' => 'Greater Boston Section',
        ),
        'gulfport-section' =>
        array(
            'state' => 'Mississippi',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Gulfport Section',
        ),
        'cincinnati-section' =>
        array(
            'state' => 'Ohio',
            'region' => 'Midwest',
            'nation' => 'USA',
            'name' => 'Cincinnati Section',
        ),
        'syracuse-section' =>
        array(
            'state' => 'New York',
            'region' => 'Northeast',
            'nation' => 'USA',
            'name' => 'Syracuse Section',
        ),
        'columbia-south-carolina-section' =>
        array(
            'state' => 'South Carolina',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Columbia South Carolina Section',
        ),
        'long-beach-section' =>
        array(
            'state' => 'California',
            'region' => 'West',
            'nation' => 'USA',
            'name' => 'Long Beach Section',
        ),
        'valdosta-lowndes-metro-section' =>
        array(
            'state' => 'Georgia',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Valdosta-Lowndes Metro Section',
        ),
        'denver-section' =>
        array(
            'state' => 'Colorado',
            'region' => 'West',
            'nation' => 'USA',
            'name' => 'Denver Section',
        ),
        'henry-clayton-section' =>
        array(
            'state' => 'Georgia',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Henry-Clayton Section',
        ),
        'alamance-guildford-section' =>
        array(
            'state' => 'North Carolina',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Alamance-Guildford Section',
        ),
        'richmond-section' =>
        array(
            'state' => 'Virginia',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Richmond Section',
        ),
        'metropolitan-washington-area-section' =>
        array(
            'state' => 'District of Columbia',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Metropolitan Washington Area Section',
        ),
        'houston-metropolitan-area-section' =>
        array(
            'state' => 'Texas',
            'region' => 'Southwest',
            'nation' => 'USA',
            'name' => 'Houston Metropolitan Area Section',
        ),
        'montgomery-county-section' =>
        array(
            'state' => 'Maryland',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Montgomery County Section',
        ),
        'selma-dallas-county-section' =>
        array(
            'state' => 'Alabama',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Selma-Dallas County Section',
        ),
        'tidewater-virginia-section' =>
        array(
            'state' => 'Virginia',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Tidewater Virginia Section',
        ),
        'staten-island-section' =>
        array(
            'state' => 'New York',
            'region' => 'Northeast',
            'nation' => 'USA',
            'name' => 'Staten Island Section',
        ),
        'east-oakland-hayward-section' =>
        array(
            'state' => 'California',
            'region' => 'West',
            'nation' => 'USA',
            'name' => 'East Oakland/Hayward Section',
        ),
        'greater-new-haven-section' =>
        array(
            'state' => 'Connecticut',
            'region' => 'Northeast',
            'nation' => 'USA',
            'name' => 'Greater New Haven Section',
        ),
        'greater-pocono-section' =>
        array(
            'state' => 'Pennsylvania',
            'region' => 'Northeast',
            'nation' => 'USA',
            'name' => 'Greater Pocono Section',
        ),
        'austin-section' =>
        array(
            'state' => 'Texas',
            'region' => 'Southwest',
            'nation' => 'USA',
            'name' => 'Austin Section',
        ),
        'tri-county-section' =>
        array(
            'state' => 'New Jersey',
            'region' => 'Northeast',
            'nation' => 'USA',
            'name' => 'Tri-County Section',
        ),
        'indianapolis-section' =>
        array(
            'state' => 'Indiana',
            'region' => 'Midwest',
            'nation' => 'USA',
            'name' => 'Indianapolis Section',
        ),
        'st-petersburg-metropolitan-section' =>
        array(
            'state' => 'Florida',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'St. Petersburg Metropolitan Section',
        ),
        'dekalb-section' =>
        array(
            'state' => 'Georgia',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Dekalb Section',
        ),
        'new-orleans-section' =>
        array(
            'state' => 'Louisiana',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'New Orleans Section',
        ),
        'manhattan-section' => array(
            'state' => 'New York',
            'region' => 'Northeast',
            'nation' => 'USA',
            'name' => 'Manhattan Section',
        ),
        'greenville-pitt-county-area-section' => array(
            'state' => 'North Carolina',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Greenville Pitt County Area Section',
        ),
        'inland-empire-section' => array(
            'state' => 'California',
            'region' => 'West',
            'nation' => 'USA',
            'name' => 'Inland-Empire Section',
        ),
        'durham-section' => array(
            'state' => 'North Carolina',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Durham Section',
        ),
        'university-of-miami-collegiate-section' => array(
            'state' => 'Florida',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'University of Miami Collegiate Section',
        ),
        'university-of-north-carolina-at-chapel-hill' => array(
            'state' => 'North Carolina',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'University of North Carolina at Chapel Hill',
        ),
        'fayetteville-state-university-section' => array(
            'state' => 'North Carolina',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Fayetteville State University Section',
        ),
        'indiana-state-university-collegiate-section' => array(
            'state' => 'Indiana',
            'region' => 'Midwest',
            'nation' => 'USA',
            'name' => 'Indiana State University Collegiate Section',
        ),
        'elizabeth-city-state-university-collegiate-section' => array(
            'state' => 'North Carolina',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Elizabeth City State University Collegiate Section',
        ),
        'frostburg-state-university-collegiate-section' => array(
            'state' => 'Maryland',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Frostburg State University Collegiate Section',
        ),
        'bethune-cookman-university-collegiate-section' => array(
            'state' => 'Florida',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Bethune-Cookman University Collegiate Section',
        ),
        'johnson-c-smith-university' => array(
            'state' => 'North Carolina',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Johnson C. Smith University',
        ),
        'east-stroudsburg-university-collegiate-section' => array(
            'state' => 'Pennsylvania',
            'region' => 'Northeast',
            'nation' => 'USA',
            'name' => 'East Stroudsburg University Collegiate Section',
        ),
        'kean-university-collegiate-section' => array(
            'state' => 'New Jersey',
            'region' => 'Northeast',
            'nation' => 'USA',
            'name' => 'Kean University Collegiate Section',
        ),
        'clark-atlanta-university-collegiate-section' => array(
            'state' => 'Georgia',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Clark Atlanta University Collegiate Section',
        ),
        'kennesaw-state-university-collegiate-section' => array(
            'state' => 'Georgia',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Kennesaw State University Collegiate Section',
        ),
        'passaic-county-section' => array(
            'state' => 'New Jersey',
            'region' => 'Northeast',
            'nation' => 'USA',
            'name' => 'Passaic County Section',
        ),
        'golden-gate-section' => array(
            'state' => 'California',
            'region' => 'West',
            'nation' => 'USA',
            'name' => 'Golden Gate Section',
        ),
        'suffolk-county-section' => array(
            'state' => 'New York',
            'region' => 'Northeast',
            'nation' => 'USA',
            'name' => 'Suffolk County Section',
        ),
        'north-bronx-section' => array(
            'state' => 'New York',
            'region' => 'Northeast',
            'nation' => 'USA',
            'name' => 'North Bronx Section',
        ),
        'columbus-ohio-section' => array(
            'state' => 'Ohio',
            'region' => 'Midwest',
            'nation' => 'USA',
            'name' => 'Columbus Ohio Section',
        ),
        'beaumont-southeast-texas-area-section' => array(
            'state' => 'Texas',
            'region' => 'Southwest',
            'nation' => 'USA',
            'name' => 'Beaumont-Southeast Texas Area Section',
        ),
        'mary-mcleod-bethune-section-ca' => array(
            'state' => 'California',
            'region' => 'West',
            'nation' => 'USA',
            'name' => 'Mary McLeod Bethune Section CA',
        ),
        'north-shore-area-section' => array(
            'state' => 'Massachusetts',
            'region' => 'Northeast',
            'nation' => 'USA',
            'name' => 'North Shore Area Section',
        ),
        'florence-section' => array(
            'state' => 'South Carolina',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Florence Section',
        ),
        'greater-atlanta-section' => array(
            'state' => 'Georgia',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Greater Atlanta Section',
        ),
        'young-legends-san-gabriel-valley-youth-section' => array(
            'state' => 'California',
            'region' => 'West',
            'nation' => 'USA',
            'name' => 'Young Legends San Gabriel Valley Youth Section',
        ),
        'new-london-county-section' => array(
            'state' => 'Connecticut',
            'region' => 'Northeast',
            'nation' => 'USA',
            'name' => 'New London County Section',
        ),
        'san-diego-section' => array(
            'state' => 'California',
            'region' => 'West',
            'nation' => 'USA',
            'name' => 'San Diego Section',
        ),
        'stafford-fredericksburg-section' => array(
            'state' => 'Virginia',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Stafford-Fredericksburg Section',
        ),
        'metropolitan-dade-county-section' => array(
            'state' => 'Florida',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Metropolitan Dade County Section',
        ),
        'northeastern-north-carolina-section' => array(
            'state' => 'North Carolina',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Northeastern North Carolina Section',
        ),
        'westchester-section' => array(
            'state' => 'New York',
            'region' => 'Northeast',
            'nation' => 'USA',
            'name' => 'Westchester Section',
        ),
        'east-palo-alto-section' => array(
            'state' => 'California',
            'region' => 'West',
            'nation' => 'USA',
            'name' => 'East Palo Alto Section',
        ),
        'fayetteville-area-section' => array(
            'state' => 'North Carolina',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Fayetteville Area Section',
        ),
        'dorothy-i-height-quad-counties-section' => array(
            'state' => 'New Jersey',
            'region' => 'Northeast',
            'nation' => 'USA',
            'name' => 'Dorothy I. Height Quad-Counties Section',
        ),
        'montclair-section' => array(
            'state' => 'New Jersey',
            'region' => 'Northeast',
            'nation' => 'USA',
            'name' => 'Montclair Section',
        ),
        'potomac-valley-section' => array(
            'state' => 'Maryland',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Potomac Valley Section',
        ),
        'colleton-section' => array(
            'state' => 'South Carolina',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Colleton Section',
        ),
        'flatbush-section' => array(
            'state' => 'New York',
            'region' => 'Northeast',
            'nation' => 'USA',
            'name' => 'Flatbush Section',
        ),
        'rankin-mon-valley-pittsburgh-section' => array(
            'state' => 'Pennsylvania',
            'region' => 'Northeast',
            'nation' => 'USA',
            'name' => 'Rankin/Mon Valley/Pittsburgh Section',
        ),
        'compton-section' => array(
            'state' => 'California',
            'region' => 'West',
            'nation' => 'USA',
            'name' => 'Compton Section',
        ),
        'louisville-section' => array(
            'state' => 'Kentucky',
            'region' => 'Midwest',
            'nation' => 'USA',
            'name' => 'Louisville Section',
        ),
        'cape-fear-area-section' => array(
            'state' => 'North Carolina',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Cape Fear Area Section',
        ),
        'metro-jackson-section' => array(
            'state' => 'Mississippi',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Metro-Jackson Section',
        ),
        'northern-virginia-section' => array(
            'state' => 'Virginia',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Northern Virginia Section',
        ),
        'raritan-valley-section' => array(
            'state' => 'New Jersey',
            'region' => 'Northeast',
            'nation' => 'USA',
            'name' => 'Raritan Valley Section',
        ),
        'lee-county-section' => array(
            'state' => 'Alabama',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Lee County Section',
        ),
        'cuyahoga-county-section' => array(
            'state' => 'Ohio',
            'region' => 'Midwest',
            'nation' => 'USA',
            'name' => 'Cuyahoga County Section',
        ),
        'darlington-county-section' => array(
            'state' => 'South Carolina',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Darlington County Section',
        ),
        'lawrence-indiana-section' => array(
            'state' => 'Indiana',
            'region' => 'Midwest',
            'nation' => 'USA',
            'name' => 'Lawrence Indiana Section',
        ),
        'metropolitan-sun-of-arizona-section' => array(
            'state' => 'Arizona',
            'region' => 'West',
            'nation' => 'USA',
            'name' => 'Metropolitan Sun of Arizona Section',
        ),
        'albuquerque-section' => array(
            'state' => 'New Mexico',
            'region' => 'West',
            'nation' => 'USA',
            'name' => 'Albuquerque Section',
        ),
        'okolona-section' => array(
            'state' => 'Mississippi',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Okolona Section',
        ),
        'clarke-county-section' => array(
            'state' => 'Georgia',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Clarke County Section',
        ),
        'greenville-county-section' => array(
            'state' => 'South Carolina',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Greenville County Section',
        ),
        'minnie-h-goodlow-page-section' => array(
            'state' => 'Texas',
            'region' => 'Southwest',
            'nation' => 'USA',
            'name' => 'Minnie H. Goodlow Page Section',
        ),
        'east-bronx-section' => array(
            'state' => 'New York',
            'region' => 'Northeast',
            'nation' => 'USA',
            'name' => 'East Bronx Section',
        ),
        'rahway-section' => array(
            'state' => 'New Jersey',
            'region' => 'Northeast',
            'nation' => 'USA',
            'name' => 'Rahway Section',
        ),
        'macomb-county-section' => array(
            'state' => 'Michigan',
            'region' => 'Midwest',
            'nation' => 'USA',
            'name' => 'Macomb County Section',
        ),
        'san-gabriel-valley-section' => array(
            'state' => 'California',
            'region' => 'West',
            'nation' => 'USA',
            'name' => 'San Gabriel Valley Section',
        ),
        'pomona-valley-section' => array(
            'state' => 'California',
            'region' => 'West',
            'nation' => 'USA',
            'name' => 'Pomona Valley Section',
        ),
        'vallejo-section' => array(
            'state' => 'California',
            'region' => 'West',
            'nation' => 'USA',
            'name' => 'Vallejo Section',
        ),
        'high-desert-section' => array(
            'state' => 'California',
            'region' => 'West',
            'nation' => 'USA',
            'name' => 'High Desert Section',
        ),
        'plainfield-scotch-plains-section' => array(
            'state' => 'New Jersey',
            'region' => 'Northeast',
            'nation' => 'USA',
            'name' => 'Plainfield Scotch Plains Section',
        ),
        'roselle-section' => array(
            'state' => 'New Jersey',
            'region' => 'Northeast',
            'nation' => 'USA',
            'name' => 'Roselle Section',
        ),
        'metropolitan-womens-network-section' => array(
            'state' => 'New York',
            'region' => 'Northeast',
            'nation' => 'USA',
            'name' => 'Metropolitan Women\'s Network Section',
        ),
        'rockford-section' => array(
            'state' => 'Illinois',
            'region' => 'Midwest',
            'nation' => 'USA',
            'name' => 'Rockford Section',
        ),
        'wilcox-county-section' => array(
            'state' => 'Alabama',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Wilcox County Section',
        ),
        'mary-mcleod-bethune-section-sc' => array(
            'state' => 'South Carolina',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Mary McLeod Bethune Section SC',
        ),
        'middle-tennessee-section' => array(
            'state' => 'Tennessee',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Middle Tennessee Section',
        ),
        'rockdale-newton-section' => array(
            'state' => 'Georgia',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Rockdale-Newton Section',
        ),
        't-mathis-hawkins-section' => array(
            'state' => 'Georgia',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'T. Mathis Hawkins Section',
        ),
        'daytona-beach-section' => array(
            'state' => 'Florida',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Daytona Beach Section',
        ),
        'tri-county-section-nj' => array(
            'state' => 'New Jersey',
            'region' => 'Northeast',
            'nation' => 'USA',
            'name' => 'Tri-County Section NJ',
        ),
        'metropolitan-greensboro-area-section' => array(
            'state' => 'North Carolina',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Metropolitan Greensboro Area Section',
        ),
        'sandusky-ohio-section' => array(
            'state' => 'Ohio',
            'region' => 'Midwest',
            'nation' => 'USA',
            'name' => 'Sandusky Ohio Section',
        ),
        'long-island-cross-county-section' => array(
            'state' => 'New York',
            'region' => 'Northeast',
            'nation' => 'USA',
            'name' => 'Long Island Cross County Section',
        ),
        'riverside-section-ca' => array(
            'state' => 'California',
            'region' => 'West',
            'nation' => 'USA',
            'name' => 'Riverside Section CA',
        ),
        'gwinnett-county-area-section' => array(
            'state' => 'Georgia',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Gwinnett County Area Section',
        ),
        'greater-elizabeth-area-section' => array(
            'state' => 'New Jersey',
            'region' => 'Northeast',
            'nation' => 'USA',
            'name' => 'Greater Elizabeth Area Section',
        ),
        'merced-county-section' => array(
            'state' => 'California',
            'region' => 'West',
            'nation' => 'USA',
            'name' => 'Merced County Section',
        ),
        'los-angeles-section' => array(
            'state' => 'California',
            'region' => 'West',
            'nation' => 'USA',
            'name' => 'Los Angeles Section',
        ),
        'virginia-beach-section' => array(
            'state' => 'Virginia',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Virginia Beach Section',
        ),
        'south-suburban-chicago-section' => array(
            'state' => 'Illinois',
            'region' => 'Midwest',
            'nation' => 'USA',
            'name' => 'South Suburban Chicago Section',
        ),
        'alton-section' => array(
            'state' => 'Illinois',
            'region' => 'Midwest',
            'nation' => 'USA',
            'name' => 'Alton Section',
        ),
        'san-antonio-ruth-jones-mcclendon' => array(
            'state' => 'Texas',
            'region' => 'Southwest',
            'nation' => 'USA',
            'name' => 'San Antonio - Ruth Jones McClendon',
        ),
        'eastern-howard-county-section' => array(
            'state' => 'Maryland',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Eastern Howard County Section',
        ),
        'lake-sumter-marion-counties-section' => array(
            'state' => 'Florida',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Lake Sumter Marion Counties Section',
        ),
        'jacksonville-section' => array(
            'state' => 'Florida',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Jacksonville Section',
        ),
        'fairfield-suisun-vacaville-section' => array(
            'state' => 'California',
            'region' => 'West',
            'nation' => 'USA',
            'name' => 'Fairfield-Suisun-Vacaville Section',
        ),
        'gary-section' => array(
            'state' => 'Indiana',
            'region' => 'Midwest',
            'nation' => 'USA',
            'name' => 'Gary Section',
        ),
        'bethune-leonard-section' => array(
            'state' => 'Florida',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Bethune-Leonard Section',
        ),
        'capital-city-life-member-guild' => array(
            'state' => 'District of Columbia',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Capital City Life Member Guild',
        ),
        'district-of-columbia-section-2' => array(
            'state' => 'District of Columbia',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'District of Columbia Section #2',
        ),
        'philadelphia-section' => array(
            'state' => 'Pennsylvania',
            'region' => 'Northeast',
            'nation' => 'USA',
            'name' => 'Philadelphia Section',
        ),
        'nassau-county-section' => array(
            'state' => 'New York',
            'region' => 'Northeast',
            'nation' => 'USA',
            'name' => 'Nassau County Section',
        ),
        'charlotte-section' => array(
            'state' => 'North Carolina',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Charlotte Section',
        ),
        'texarkana-bi-city-section' => array(
            'state' => 'Texas',
            'region' => 'Southwest',
            'nation' => 'USA',
            'name' => 'Texarkana Bi-City Section',
        ),
        'seattle-section' => array(
            'state' => 'Washington',
            'region' => 'West',
            'nation' => 'USA',
            'name' => 'Seattle Section',
        ),
        'greater-trinity-section' => array(
            'state' => 'Texas',
            'region' => 'Southwest',
            'nation' => 'USA',
            'name' => 'Greater Trinity Section',
        ),
        'savannah-section' => array(
            'state' => 'Georgia',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Savannah Section',
        ),
        'alameda-county-section' => array(
            'state' => 'California',
            'region' => 'West',
            'nation' => 'USA',
            'name' => 'Alameda County Section',
        ),
        'barbara-jordan-houston-section' => array(
            'state' => 'Texas',
            'region' => 'Southwest',
            'nation' => 'USA',
            'name' => 'Barbara Jordan-Houston Section',
        ),
        'detroit-section' => array(
            'state' => 'Michigan',
            'region' => 'Midwest',
            'nation' => 'USA',
            'name' => 'Detroit Section',
        ),
        'coastal-georgia-area-section' => array(
            'state' => 'Georgia',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Coastal Georgia Area Section',
        ),
        'hartford-section' => array(
            'state' => 'Connecticut',
            'region' => 'Northeast',
            'nation' => 'USA',
            'name' => 'Hartford Section',
        ),
        'lexington-central-kentucky-section' => array(
            'state' => 'Kentucky',
            'region' => 'Midwest',
            'nation' => 'USA',
            'name' => 'Lexington-Central Kentucky Section',
        ),
        'chesterfield-metro-area-section-va' => array(
            'state' => 'Virginia',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Chesterfield Metro Area Section VA',
        ),
        'hudson-valley-section' => array(
            'state' => 'New York',
            'region' => 'Northeast',
            'nation' => 'USA',
            'name' => 'Hudson Valley Section',
        ),
        'co-op-city-section' => array(
            'state' => 'New York',
            'region' => 'Northeast',
            'nation' => 'USA',
            'name' => 'Co-op City Section',
        ),
        'dallas-southwest-section' => array(
            'state' => 'Texas',
            'region' => 'Southwest',
            'nation' => 'USA',
            'name' => 'Dallas Southwest Section',
        ),
        'capital-area-section-of-north-carolina' => array(
            'state' => 'North Carolina',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Capital Area Section of North Carolina',
        ),
        'western-reserve-section' => array(
            'state' => 'Ohio',
            'region' => 'Midwest',
            'nation' => 'USA',
            'name' => 'Western Reserve Section',
        ),
        'midwood-vicinity-section' => array(
            'state' => 'New York',
            'region' => 'Northeast',
            'nation' => 'USA',
            'name' => 'Midwood & Vicinity Section',
        ),
        'central-savannah-river-area-section-augusta-ga' => array(
            'state' => 'Georgia',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Central Savannah River Area Section—Augusta, GA',
        ),
        'east-bay-area-section' => array(
            'state' => 'California',
            'region' => 'West',
            'nation' => 'USA',
            'name' => 'East Bay Area Section',
        ),
        'reston-dulles-section' => array(
            'state' => 'Virginia',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Reston-Dulles Section',
        ),
        'mercer-county-section' => array(
            'state' => 'New Jersey',
            'region' => 'Northeast',
            'nation' => 'USA',
            'name' => 'Mercer County Section',
        ),
        'sacramento-valley-section' => array(
            'state' => 'California',
            'region' => 'West',
            'nation' => 'USA',
            'name' => 'Sacramento Valley Section',
        ),
        'magnolia-bethune-height-section' => array(
            'state' => 'Mississippi',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Magnolia Bethune-Height Section',
        ),
        'greater-harrisburg-section' => array(
            'state' => 'Pennsylvania',
            'region' => 'Northeast',
            'nation' => 'USA',
            'name' => 'Greater Harrisburg Section',
        ),
        'new-dominion-section' => array(
            'state' => 'Virginia',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'New Dominion Section',
        ),
        'lorain-county-section' => array(
            'state' => 'Ohio',
            'region' => 'Midwest',
            'nation' => 'USA',
            'name' => 'Lorain County Section',
        ),
        'memphis-shelby-county-section' => array(
            'state' => 'Tennessee',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Memphis/Shelby County Section',
        ),
        'north-fulton-area-section' => array(
            'state' => 'Georgia',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'North Fulton Area Section',
        ),
        'view-park-section' => array(
            'state' => 'California',
            'region' => 'West',
            'nation' => 'USA',
            'name' => 'View Park Section',
        ),
        'milwaukee-section' => array(
            'state' => 'Wisconsin',
            'region' => 'Midwest',
            'nation' => 'USA',
            'name' => 'Milwaukee Section',
        ),
        'ame-zion-nw-chicago-district-section' => array(
            'state' => 'Illinois',
            'region' => 'Midwest',
            'nation' => 'USA',
            'name' => 'A.M.E. Zion NW-Chicago District Section',
        ),
        'cleveland-section' => array(
            'state' => 'Ohio',
            'region' => 'Midwest',
            'nation' => 'USA',
            'name' => 'Cleveland Section',
        ),
        'moreno-valley-life-member-guild' => array(
            'state' => 'California',
            'region' => 'West',
            'nation' => 'USA',
            'name' => 'Moreno Valley Life Member Guild',
        ),
        'columbus-georgia-area-section' => array(
            'state' => 'Georgia',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Columbus Georgia Area Section',
        ),
        'central-florida-section' => array(
            'state' => 'Florida',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Central Florida Section',
        ),
        'section-of-the-oranges' => array(
            'state' => 'New Jersey',
            'region' => 'Northeast',
            'nation' => 'USA',
            'name' => 'Section of the Oranges',
        ),
        'huntington-section' => array(
            'state' => 'West Virginia',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Huntington Section',
        ),
        'champaign-county-section' => array(
            'state' => 'Illinois',
            'region' => 'Midwest',
            'nation' => 'USA',
            'name' => 'Champaign County Section',
        ),
        'norfolk-section' => array(
            'state' => 'Virginia',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Norfolk Section',
        ),
        'orange-county-section' => array(
            'state' => 'California',
            'region' => 'West',
            'nation' => 'USA',
            'name' => 'Orange County Section',
        ),
        'florence-bethune-section' => array(
            'state' => 'South Carolina',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Florence-Bethune Section',
        ),
        'mary-mcleod-bethune-section-sc' => array(
            'state' => 'South Carolina',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Mary McLeod Bethune Section SC',
        ),
        'laurens-county-section' => array(
            'state' => 'South Carolina',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Laurens County Section',
        ),
        'indiana-life-member-guild' => array(
            'state' => 'Indiana',
            'region' => 'Midwest',
            'nation' => 'USA',
            'name' => 'Indiana Life Member Guild',
        ),
        'suburban-dallas-desoto-section' => array(
            'state' => 'Texas',
            'region' => 'Southwest',
            'nation' => 'USA',
            'name' => 'Suburban Dallas/DeSoto Section',
        ),
        'greater-bridgeport-section' => array(
            'state' => 'Connecticut',
            'region' => 'Northeast',
            'nation' => 'USA',
            'name' => 'Greater Bridgeport Section',
        ),
        'brevard-county-section' => array(
            'state' => 'Florida',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Brevard County Section',
        ),
        'clarendon-section' => array(
            'state' => 'South Carolina',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Clarendon Section',
        ),
        'northwest-virginia-section' => array(
            'state' => 'Virginia',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Northwest Virginia Section',
        ),
        'bertha-black-rhoda-section' => array(
            'state' => 'Alabama',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Bertha Black Rhoda Section',
        ),
        'williamsburg-section' => array(
            'state' => 'Virginia',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Williamsburg Section',
        ),
        'booker-clark-section' => array(
            'state' => 'Mississippi',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Booker Clark Section',
        ),
        'chicago-central-section' => array(
            'state' => 'Illinois',
            'region' => 'Midwest',
            'nation' => 'USA',
            'name' => 'Chicago Central Section',
        ),
        'pensacola-section' => array(
            'state' => 'Florida',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Pensacola Section',
        ),
        'san-francisco-section' => array(
            'state' => 'California',
            'region' => 'West',
            'nation' => 'USA',
            'name' => 'San Francisco Section',
        ),
        'omaha-section' => array(
            'state' => 'Nebraska',
            'region' => 'Midwest',
            'nation' => 'USA',
            'name' => 'Omaha Section',
        ),
        'chicago-midwest-section' => array(
            'state' => 'Illinois',
            'region' => 'Midwest',
            'nation' => 'USA',
            'name' => 'Chicago Midwest Section',
        ),
        'clark-county-springfield-section' => array(
            'state' => 'Ohio',
            'region' => 'Midwest',
            'nation' => 'USA',
            'name' => 'Clark County Springfield Section',
        ),
        'montgomery-alabama-section' => array(
            'state' => 'Alabama',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Montgomery Alabama Section',
        ),
        'youngstown-section' => array(
            'state' => 'Ohio',
            'region' => 'Midwest',
            'nation' => 'USA',
            'name' => 'Youngstown Section',
        ),
        'natchez-section' => array(
            'state' => 'Mississippi',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Natchez Section',
        ),
        'new-jersey-life-member-guild' => array(
            'state' => 'New Jersey',
            'region' => 'Northeast',
            'nation' => 'USA',
            'name' => 'New Jersey Life Member Guild',
        ),
        'ethele-scott-section' => array(
            'state' => 'Georgia',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Ethele Scott Section',
        ),
        'bronx-section' => array(
            'state' => 'New York',
            'region' => 'Northeast',
            'nation' => 'USA',
            'name' => 'Bronx Section',
        ),
        'kosciusko-attala-section' => array(
            'state' => 'Mississippi',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Kosciusko-Attala Section',
        ),
        'richmond-life-member-guild' => array(
            'state' => 'Virginia',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Richmond Life Member Guild',
        ),
        'dekalb-pacesetters-life-member-guild' => array(
            'state' => 'Georgia',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Dekalb Pacesetters Life Member Guild',
        ),
        'dayton-section' => array(
            'state' => 'Ohio',
            'region' => 'Midwest',
            'nation' => 'USA',
            'name' => 'Dayton Section',
        ),
        'elease-knight-section' => array(
            'state' => 'Alabama',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Elease Knight Section',
        ),
        'dayton-springfield-life-member-guild' => array(
            'state' => 'Ohio',
            'region' => 'Midwest',
            'nation' => 'USA',
            'name' => 'Dayton-Springfield Life Member Guild',
        ),
        'west-volusia-seminole-section' => array(
            'state' => 'Florida',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'West Volusia/Seminole Section',
        ),
        'mary-mcleod-bethune-section-ca' => array(
            'state' => 'California',
            'region' => 'West',
            'nation' => 'USA',
            'name' => 'Mary McLeod Bethune Section CA',
        ),
        'northern-california-life-member-guild' => array(
            'state' => 'California',
            'region' => 'West',
            'nation' => 'USA',
            'name' => 'Northern California Life Member Guild',
        ),
        'greater-washington-life-member-guild' => array(
            'state' => 'District of Columbia',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Greater Washington Life Member Guild',
        ),
        'greenwood-section' => array(
            'state' => 'Mississippi',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Greenwood Section',
        ),
        'birmingham-metropolitan-section' => array(
            'state' => 'Alabama',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Birmingham Metropolitan Section',
        ),
        'cosmopolitan-chicago-section' => array(
            'state' => 'Illinois',
            'region' => 'Midwest',
            'nation' => 'USA',
            'name' => 'Cosmopolitan Chicago Section',
        ),
        'southern-california-life-member-guild' => array(
            'state' => 'California',
            'region' => 'West',
            'nation' => 'USA',
            'name' => 'Southern California Life Member Guild',
        ),
        'gateway-metropolitan-section' => array(
            'state' => 'Missouri',
            'region' => 'Midwest',
            'nation' => 'USA',
            'name' => 'Gateway Metropolitan Section',
        ),
        'rose-city-tyler-section' => array(
            'state' => 'Texas',
            'region' => 'Southwest',
            'nation' => 'USA',
            'name' => 'Rose City Tyler Section',
        ),
        'albany-flint-river-area-section' => array(
            'state' => 'Georgia',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Albany Flint River Area',
        ),
        'northeast-area-mississippi-section' => array(
            'state' => 'Mississippi',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Northeast Area Mississippi Section',
        ),
        'monmouth-university-section' => array(
            'state' => 'New Jersey',
            'region' => 'Northeast',
            'nation' => 'USA',
            'name' => 'Monmouth University',
        ),
        'columbus-youth-section' => array(
            'state' => 'Ohio',
            'region' => 'Midwest',
            'nation' => 'USA',
            'name' => 'Columbus Youth Section',
        ),
        'laurel-section' => array(
            'state' => 'Mississippi',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Laurel Section',
        ),
        'greater-tallahassee-section' => array(
            'state' => 'Florida',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Greater Tallahassee Section',
        ),
        'university-of-connecticut-collegiate-section' => array(
            'state' => 'Connecticut',
            'region' => 'Northeast',
            'nation' => 'USA',
            'name' => 'University of Connecticut Collegiate Section',
        ),
        'temple-university-section' => array(
            'state' => 'Pennsylvania',
            'region' => 'Northeast',
            'nation' => 'USA',
            'name' => 'Temple University Section',
        ),
        'university-of-north-carolina-charlotte-collegiate-section' => array(
            'state' => 'North Carolina',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'University of North Carolina Charlotte Collegiate Section',
        ),
        'university-of-south-florida-at-tampa' => array(
            'state' => 'Florida',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'University of South Florida at Tampa',
        ),
        'valdosta-state-university-collegiate-section' => array(
            'state' => 'Georgia',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Valdosta State University Collegiate Section',
        ),
        'savannah-state-university-collegiate-section' => array(
            'state' => 'Georgia',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Savannah State University Collegiate Section',
        ),
        'university-of-west-georgia-collegiate-section' => array(
            'state' => 'Georgia',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'University of West Georgia Collegiate Section',
        ),
        'spelman-college-collegiate-section' => array(
            'state' => 'Georgia',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Spelman College Collegiate Section',
        ),
        'west-chester-university-section' => array(
            'state' => 'Pennsylvania',
            'region' => 'Northeast',
            'nation' => 'USA',
            'name' => 'West Chester University',
        ),
        'salisbury-university-collegiate-section' => array(
            'state' => 'Maryland',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Salisbury University Collegiate Section',
        ),
        'university-of-michigan-section' => array(
            'state' => 'Michigan',
            'region' => 'Midwest',
            'nation' => 'USA',
            'name' => 'University of Michigan',
        ),
        'university-of-north-carolina-at-pembroke' => array(
            'state' => 'North Carolina',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'University of North Carolina at Pembroke',
        ),
        'university-of-georgia-section' => array(
            'state' => 'Georgia',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'University of Georgia Section',
        ),
        'tennessee-state-university-collegiate-section' => array(
            'state' => 'Tennessee',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Tennessee State University Collegiate Section',
        ),
        'saint-augustines-university' => array(
            'state' => 'North Carolina',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Saint Augustine\'s University',
        ),
        'university-of-west-florida' => array(
            'state' => 'Florida',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'University of West Florida',
        ),
        'university-of-north-florida-section' => array(
            'state' => 'Florida',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'University of North Florida Section',
        ),
        'university-of-maryland-collegiate-section' => array(
            'state' => 'Maryland',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'University of Maryland Collegiate Section',
        ),
        'augusta-university' => array(
            'state' => 'Georgia',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Augusta University',
        ),
        'university-of-central-florida' => array(
            'state' => 'Florida',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'University of Central Florida',
        ),
        'university-of-alabama-collegiate-section' => array(
            'state' => 'Alabama',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'University of Alabama Collegiate Section',
        ),
        'tuskegee-university' => array(
            'state' => 'Alabama',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Tuskegee University',
        ),
        'albany-state-university' => array(
            'state' => 'Georgia',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Albany State University',
        ),
        'howard-university-collegiate-section' => array(
            'state' => 'District of Columbia',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Howard University Collegiate Section',
        ),
        'florida-am-university-collegiate-section' => array(
            'state' => 'Florida',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Florida A&M University Collegiate Section',
        ),
        'florida-international-university-collegiate-section' => array(
            'state' => 'Florida',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Florida International University Collegiate Section',
        ),
        'georgia-state-university-section' => array(
            'state' => 'Georgia',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Georgia State University Section',
        ),
        'morgan-state-university' => array(
            'state' => 'Maryland',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Morgan State University',
        ),
        'georgia-southern-university' => array(
            'state' => 'Georgia',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Georgia Southern University',
        ),
        'towson-university-collegiate-section' => array(
            'state' => 'Maryland',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Towson University Collegiate Section',
        ),
        'north-carolina-at-state-university-collegiate-section' => array(
            'state' => 'North Carolina',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'North Carolina A&T State University Collegiate Section',
        ),
        'east-carolina-university-collegiate-section' => array(
            'state' => 'North Carolina',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'East Carolina University Collegiate Section',
        ),
        'bowie-state-university-collegiate-section' => array(
            'state' => 'Maryland',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Bowie State University Collegiate Section',
        ),
        'mercer-university-collegiate-section' => array(
            'state' => 'Georgia',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Mercer University Collegiate Section',
        ),
        'winston-salem-state-university-section' => array(
            'state' => 'North Carolina',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Winston-Salem State University Section',
        ),
        'penn-state' => array(
            'state' => 'Pennsylvania',
            'region' => 'Northeast',
            'nation' => 'USA',
            'name' => 'Penn State',
        ),
        'city-college-of-new-york-collegiate-section' => array(
            'state' => 'New York',
            'region' => 'Northeast',
            'nation' => 'USA',
            'name' => 'City College of New York Collegiate Section',
        ),
        'clayton-state-university-collegiate-section' => array(
            'state' => 'Georgia',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Clayton State University Collegiate Section',
        ),
        'robert-morris-university-collegiate-section' => array(
            'state' => 'Pennsylvania',
            'region' => 'Northeast',
            'nation' => 'USA',
            'name' => 'Robert Morris University Collegiate Section',
        ),
        'florida-atlantic-university' => array(
            'state' => 'Florida',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Florida Atlantic University',
        ),
        'the-george-washington-university-section' => array(
            'state' => 'District of Columbia',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'The George Washington University Section',
        ),
        'north-carolina-central-university-collegiate-section' => array(
            'state' => 'North Carolina',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'North Carolina Central University Collegiate Section',
        ),
        'widener-university-collegiate-section' => array(
            'state' => 'Pennsylvania',
            'region' => 'Northeast',
            'nation' => 'USA',
            'name' => 'Widener University Collegiate Section',
        ),
        'seton-hall-university-collegiate-section' => array(
            'state' => 'New Jersey',
            'region' => 'Northeast',
            'nation' => 'USA',
            'name' => 'Seton Hall University Collegiate Section',
        ),
        'george-mason-university-collegiate-section' => array(
            'state' => 'Virginia',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'George Mason University Collegiate Section',
        ),
        'delaware-state-university' => array(
            'state' => 'Delaware',
            'region' => 'Northeast',
            'nation' => 'USA',
            'name' => 'Delaware State University',
        ),
        'morris-college-collegiate-section' => array(
            'state' => 'South Carolina',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Morris College Collegiate Section',
        ),
        'fort-valley-state-university-collegiate-section' => array(
            'state' => 'Georgia',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Fort Valley State University Collegiate Section',
        ),
        'old-dominion-university-collegiate-section' => array(
            'state' => 'Virginia',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Old Dominion University Collegiate Section',
        ),
        'cheyney-state-university-section' => array(
            'state' => 'Pennsylvania',
            'region' => 'Northeast',
            'nation' => 'USA',
            'name' => 'Cheyney State University Section',
        ),
        'norfolk-state-university' => array(
            'state' => 'Virginia',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Norfolk State University',
        ),
        'texas-college' => array(
            'state' => 'Texas',
            'region' => 'Southwest',
            'nation' => 'USA',
            'name' => 'Texas College',
        ),
        'shaw-university-collegiate-section' => array(
            'state' => 'North Carolina',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Shaw University Collegiate Section',
        ),
        'columbus-state-university-collegiate-section' => array(
            'state' => 'Georgia',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Columbus State University Collegiate Section',
        ),
        'harris-stowe-state-university-collegiate-section' => array(
            'state' => 'Missouri',
            'region' => 'Midwest',
            'nation' => 'USA',
            'name' => 'Harris-Stowe State University Collegiate Section',
        ),
        'shippensburg-university-collegiate-section' => array(
            'state' => 'Pennsylvania',
            'region' => 'Northeast',
            'nation' => 'USA',
            'name' => 'Shippensburg University Collegiate Section',
        ),
        'chicago-state-university' => array(
            'state' => 'Illinois',
            'region' => 'Midwest',
            'nation' => 'USA',
            'name' => 'Chicago State University',
        ),
        'coppin-state-university-section' => array(
            'state' => 'Maryland',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Coppin State University Section',
        ),
        'sarah-allen-university-collegiate-section' => array(
            'state' => 'Pennsylvania',
            'region' => 'Northeast',
            'nation' => 'USA',
            'name' => 'Sarah Allen University Collegiate Section',
        ),
        'hampton-university-collegiate-section' => array(
            'state' => 'Virginia',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Hampton University Collegiate Section',
        ),
        'morris-brown-college-collegiate-section' => array(
            'state' => 'Georgia',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Morris Brown College Collegiate Section',
        ),
        'bloomsburg-university' => array(
            'state' => 'Pennsylvania',
            'region' => 'Northeast',
            'nation' => 'USA',
            'name' => 'Bloomsburg University',
        ),
        'florida-state-university-collegiate-section' => array(
            'state' => 'Florida',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Florida State University Collegiate Section',
        ),
        'elon-university-collegiate-section' => array(
            'state' => 'North Carolina',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Elon University Collegiate Section',
        ),
        'benedict-byrd' => array(
            'state' => 'South Carolina',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Benedict-Byrd',
        ),
        'claflin-collegiate-section' => array(
            'state' => 'South Carolina',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Claflin Collegiate Section',
        ),
        'state-university-of-new-york-at-albany-section' => array(
            'state' => 'New York',
            'region' => 'Northeast',
            'nation' => 'USA',
            'name' => 'State University of New York at Albany Section',
        ),
        'lincoln-university' => array(
            'state' => 'Missouri',
            'region' => 'Midwest',
            'nation' => 'USA',
            'name' => 'Lincoln University',
        ),
        'edward-waters-college-section' => array(
            'state' => 'Florida',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Edward Waters College Section',
        ),
        'arizona-state-university' => array(
            'state' => 'Arizona',
            'region' => 'West',
            'nation' => 'USA',
            'name' => 'Arizona State University',
        ),
        'bowling-green-state-university-section' => array(
            'state' => 'Ohio',
            'region' => 'Midwest',
            'nation' => 'USA',
            'name' => 'Bowling Green State University Section',
        ),
        'eastern-michigan-university-section' => array(
            'state' => 'Michigan',
            'region' => 'Midwest',
            'nation' => 'USA',
            'name' => 'Eastern Michigan University Section',
        ),
        'miles-college-collegiate-section' => array(
            'state' => 'Alabama',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Miles College Collegiate Section',
        ),
        'fisk-university-collegiate-section' => array(
            'state' => 'Tennessee',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Fisk University Collegiate Section',
        ),
        'fayetteville-area-youth-section' => array(
            'state' => 'North Carolina',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Fayetteville Area Youth Section',
        ),
        'detroit-youth-section' => array(
            'state' => 'Michigan',
            'region' => 'Midwest',
            'nation' => 'USA',
            'name' => 'Detroit Youth Section',
        ),
        'spirit-of-bethune-student-section' => array(
            'state' => 'Florida',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Spirit of Bethune Student Section',
        ),
        'san-diego-youth-section' => array(
            'state' => 'California',
            'region' => 'West',
            'nation' => 'USA',
            'name' => 'San Diego Youth Section',
        ),
        'cuyahoga-county-youth-section' => array(
            'state' => 'Ohio',
            'region' => 'Midwest',
            'nation' => 'USA',
            'name' => 'Cuyahoga County Youth Section',
        ),
        'queens-county-youth-section' => array(
            'state' => 'New York',
            'region' => 'Northeast',
            'nation' => 'USA',
            'name' => 'Queens County Youth Section',
        ),
        'the-dreamers-youth-section-of-north-carolina' => array(
            'state' => 'North Carolina',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'The Dreamers Youth Section of North Carolina',
        ),
        'alton-youth-section' => array(
            'state' => 'Illinois',
            'region' => 'Midwest',
            'nation' => 'USA',
            'name' => 'Alton Youth Section',
        ),
        'female-achievers-maintaining-excellence' => array(
            'state' => 'Florida',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Female Achievers Maintaining Excellence',
        ),
        'dekalb-black-pearls-ncnw-youth-section' => array(
            'state' => 'Georgia',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Dekalb Black Pearls NCNW Youth Section',
        ),
        'richmond-youth-section-the-young-leaders' => array(
            'state' => 'Virginia',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Richmond Youth Section (The Young Leaders)',
        ),
        'brooklyn-youth-section' => array(
            'state' => 'New York',
            'region' => 'Northeast',
            'nation' => 'USA',
            'name' => 'Brooklyn Youth Section',
        ),
        'destinys-promise-youth-section-of-tampa-metro' => array(
            'state' => 'Florida',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Destiny\'s Promise Youth Section of Tampa Metro',
        ),
        'newport-news-hampton-youth-section' => array(
            'state' => 'Virginia',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Newport News-Hampton Youth Section',
        ),
        'staten-island-youth-section' => array(
            'state' => 'New York',
            'region' => 'Northeast',
            'nation' => 'USA',
            'name' => 'Staten Island Youth',
        ),
        'new-vision-youth-section' => array(
            'state' => 'North Carolina',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'New Vision Youth Section',
        ),
        'bolivar-county-youth-section-mississippi' => array(
            'state' => 'Mississippi',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Bolivar County Youth Section Mississippi',
        ),
        'bethune-leonard-youth-section' => array(
            'state' => 'Florida',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Bethune-Leonard Youth Section',
        ),
        'durham-youth-section' => array(
            'state' => 'North Carolina',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Durham Youth Section',
        ),
        'minnie-h-goodlow-page-youth-section' => array(
            'state' => 'Texas',
            'region' => 'Southwest',
            'nation' => 'USA',
            'name' => 'Minnie H. Goodlow Page Youth Section',
        ),
        'jacksonville-youth-section-fletcher-high-school' => array(
            'state' => 'Florida',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Jacksonville Youth Section (Fletcher High School)',
        ),
        'broward-county-youth-section' => array(
            'state' => 'Florida',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Broward County Youth Section',
        ),
        'dayton-youth-section' => array(
            'state' => 'Ohio',
            'region' => 'Midwest',
            'nation' => 'USA',
            'name' => 'Dayton Youth Section',
        ),
        'co-op-city-youth-section' => array(
            'state' => 'New York',
            'region' => 'Northeast',
            'nation' => 'USA',
            'name' => 'Co-op City Youth Section',
        ),
        'wilcox-county-youth-section' => array(
            'state' => 'Alabama',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Wilcox County Youth',
        ),
        'barbara-jordan-houston-youth-section' => array(
            'state' => 'Texas',
            'region' => 'Southwest',
            'nation' => 'USA',
            'name' => 'Barbara Jordan-Houston Youth Section',
        ),
        'rankin-mon-valley-pittsburgh-section' => array(
            'state' => 'Pennsylvania',
            'region' => 'Northeast',
            'nation' => 'USA',
            'name' => 'Rankin/Mon Valley/Pittsburgh Section',
        ),
        'metropolitan-womens-network-section' => array(
            'state' => 'New York',
            'region' => 'Northeast',
            'nation' => 'USA',
            'name' => 'Metropolitan Women\'s Network Section',
        ),
        'rockford-section' => array(
            'state' => 'Illinois',
            'region' => 'Midwest',
            'nation' => 'USA',
            'name' => 'Rockford Section',
        ),
        'middle-tennessee-section' => array(
            'state' => 'Tennessee',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Middle Tennessee Section',
        ),
        'south-suburban-chicago-section' => array(
            'state' => 'Illinois',
            'region' => 'Midwest',
            'nation' => 'USA',
            'name' => 'South Suburban Chicago Section',
        ),
        'magnolia-bethune-height-section' => array(
            'state' => 'Mississippi',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Magnolia Bethune-Height Section',
        ),
        'view-park-section' => array(
            'state' => 'California',
            'region' => 'West',
            'nation' => 'USA',
            'name' => 'View Park Section',
        ),
        'ame-zion-nw-chicago-district-section' => array(
            'state' => 'Illinois',
            'region' => 'Midwest',
            'nation' => 'USA',
            'name' => 'A.M.E. Zion NW-Chicago District Section',
        ),
        'chicago-central-section' => array(
            'state' => 'Illinois',
            'region' => 'Midwest',
            'nation' => 'USA',
            'name' => 'Chicago Central Section',
        ),
        'albany-flint-river-area-section' => array(
            'state' => 'Georgia',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Albany Flint River Area',
        ),
        'monmouth-university-section' => array(
            'state' => 'New Jersey',
            'region' => 'Northeast',
            'nation' => 'USA',
            'name' => 'Monmouth University',
        ),
        'west-chester-university-section' => array(
            'state' => 'Pennsylvania',
            'region' => 'Northeast',
            'nation' => 'USA',
            'name' => 'West Chester University',
        ),
        'university-of-michigan-section' => array(
            'state' => 'Michigan',
            'region' => 'Midwest',
            'nation' => 'USA',
            'name' => 'University of Michigan',
        ),
        'saint-augustines-university' => array(
            'state' => 'North Carolina',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Saint Augustine\'s University',
        ),
        'florida-am-university-collegiate-section' => array(
            'state' => 'Florida',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Florida A&M University Collegiate Section',
        ),
        'north-carolina-at-state-university-collegiate-section' => array(
            'state' => 'North Carolina',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'North Carolina A&T State University Collegiate Section',
        ),
        'san-diego-youth-section' => array(
            'state' => 'California',
            'region' => 'West',
            'nation' => 'USA',
            'name' => 'San Diego Youth Section',
        ),
        'destinys-promise-youth-section-of-tampa-metro' => array(
            'state' => 'Florida',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Destiny\'s Promise Youth Section of Tampa Metro',
        ),
        'staten-island-youth-section' => array(
            'state' => 'New York',
            'region' => 'Northeast',
            'nation' => 'USA',
            'name' => 'Staten Island Youth Section',
        ),
        'wilcox-county-youth-section' => array(
            'state' => 'Alabama',
            'region' => 'Southeast',
            'nation' => 'USA',
            'name' => 'Wilcox County Youth',
        )

    );

    // public const txnStatusNames = [
    //     'pending' => 'Pending',
    //     'ok' => 'Ok'
    // ];
    // public const payMethodNames = [
    //     'stripe' => 'Stripe',
    //     'payout' => 'Payout'
    // ];
    // public const transNames = [
    //     'campaign-earning' => 'Campaign Earning',
    //     'order-payment' => 'Order Payment',
    //     'payout' => 'Payout'
    // ];

    public function __construct()
    {
        $this->evgDir = dirname(__FILE__) . '/evg/';
    }
    public function getAllCertificates()
    {
        global $pixdb;
        return $pixdb->get('certification', ['#SRT' => 'name asc'])->data;
    }
    public function markPaymentDone($txn, $time = null, $payId = null)
    {
        global
            $pix,
            $datetime,
            $date,
            $pixdb;

        $r = (object)[];
        $r->marked = false;

        if (!is_object($txn)) {
            $txn = $pixdb->getRow(
                'transactions',
                ['id' => $txn]
            );
        }
        if ($txn) {
            // if (
            //     $txn->payload &&
            //     !is_object($txn->payload)
            // ) {
            //     $txn->payload = json_decode($txn->payload);
            // }
            // if (!is_object($txn->payload)) {
            //     $txn->payload = (object)[];
            // }
            $txnItems = $pixdb->get(
                'txn_items',
                ['txnId' => $txn->id]
            )->data;
            if (!isset($txn->status)) {
                $txn->status = 'pending';
            }
            if ($txn->status != 'success') {
                $txnMarked = false;
                $txnTitle = $txn->title;

                // action code
                $codeInc = dirname(__FILE__) . "/mark-payment-done/$txn->type.php";
                if (is_file($codeInc)) {
                    include $codeInc;
                }
                // change txn data
                if ($txnMarked) {
                    $txnMod = [
                        'status' => 'success'
                    ];

                    // check for title change
                    if ($txnTitle != $txn->title) {
                        $txnMod['title'] = $txnTitle;
                    }
                    // adding reference number
                    if ($payId) {
                        $txnMod['refNumber'] = $payId;
                    }

                    $pixdb->update(
                        'transactions',
                        ['id' => $txn->id],
                        $txnMod
                    );

                    $r->marked = true;
                }
            }
        }

        return $r;
    }
    public function getMember($id, $fields = false)
    {
        global $pixdb;
        return $pixdb->get(
            'members',
            [
                'single' => 1,
                'id' => $id
            ],
            $fields ?:
                'id, firstName, lastName, avatar, verified, email'
        );
    }
    public function getMembers($ids, $fields = false)
    {
        global $pixdb;
        return !empty($ids) ?
            $pixdb->fetchAssoc(
                'members',
                [
                    'id' => $ids
                ],
                $fields ?:
                    'id, firstName, lastName, avatar, verified, email'
            ) : [];
    }
    public function getMembershipPlan($id, $fields = false)
    {
        global $pixdb;
        return $pixdb->get(
            'membership_plans',
            [
                'single' => 1,
                'id' => $id
            ],
            $fields ?:
                'id, type, title, active, duration, ttlCharge'
        );
    }
    public function getMembershipPlans($ids, $fields = false)
    {
        global $pixdb;
        return !empty($ids) ?
            $pixdb->fetchAssoc(
                'membership_plans',
                [
                    'id' => $ids
                ],
                $fields ?:
                    'id, type, title, active, duration'
            ) : [];
    }
    public function getProducts($ids, $fields = false)
    {
        global $pixdb;
        return !empty($ids) ?
            $pixdb->fetchAssoc(
                'products',
                [
                    'id' => $ids
                ],
                $fields ?:
                    'id, code, name, amount, type'
            ) : [];
    }
    public function getLocations(
        $table,
        $ids,
        $fields = false
    ) {
        global $pixdb;
        $table = $this->getLocationsTypes($table);
        return !empty($ids) ?
            $pixdb->fetchAssoc(
                $table,
                ['id' => $ids],
                $fields ?:
                    'id,
                    name,
                    createdAt,
                    enabled,
                    updatedAt,
                    members'
            ) : [];
    }
    public function getStates(
        $ids = [],
        $fields = false
    ) {
        global $pixdb;

        $cnds = [
            '#SRT' => 'name ASC'
        ];
        if (!empty($ids)) {
            $cnds['id'] = $ids;
        }
        return $pixdb->fetchAssoc(
            'states',
            $cnds,
            $fields ?:
                'id,
                    region,
                    name,
                    createdAt,
                    enabled,
                    updatedAt,
                    members'
        );
    }
    public function getState(
        $id,
        $fields = false
    ) {
        global $pixdb;
        return $pixdb->get(
            'states',
            [
                'id' => $id,
                'single' => 1
            ],
            $fields ?:
                'id,
            region,
            name,
            createdAt,
            enabled,
            updatedAt,
            members'
        );
    }
    public function getRegions(
        $ids,
        $fields = false
    ) {
        global $pixdb;
        return !empty($ids) ?
            $pixdb->fetchAssoc(
                'regions',
                ['id' => $ids],
                $fields ?:
                    'id,
                    nation,
                    name,
                    createdAt,
                    enabled,
                    updatedAt,
                    members'
            ) : [];
    }
    public function getRegion(
        $id,
        $fields = false
    ) {
        global $pixdb;
        return $pixdb->get(
            'regions',
            [
                'id' => $id,
                'single' => 1
            ],
            $fields ?:
                'id,
            nation,
            name,
            createdAt,
            enabled,
            updatedAt,
            members'
        );
    }
    public function getNations(
        $ids = [],
        $fields = false
    ) {
        global $pixdb;

        $cnds = [
            '#SRT' => 'name ASC'
        ];
        if (!empty($ids)) {
            $cnds['id'] = $ids;
        }
        return $pixdb->fetchAssoc(
            'nations',
            $cnds,
            $fields ?:
                'id,
                name,
                createdAt,
                enabled,
                updatedAt,
                members'
        );
    }
    public function getNation(
        $id,
        $fields = false
    ) {
        global $pixdb;
        return $pixdb->get(
            'nations',
            [
                'id' => $id,
                'single' => 1
            ],
            $fields ?:
                'id,
            name,
            createdAt,
            enabled,
            updatedAt,
            members'
        );
    }
    public function getNationId($nation)
    {
        global $pixdb, $datetime;
        $id = false;
        $id = $pixdb->getRow(
            'nations',
            ['name' => $nation],
            'id'
        );

        $id ? $id = $id->id : false;

        if (
            !$id &&
            $nation !== ''
        ) {
            $id = $pixdb->insert(
                'nations',
                [
                    'name' => $nation,
                    'createdAt' => $datetime
                ]
            );
        }

        return $id;
    }
    public function getRegionId(
        $region,
        $nation = false
    ) {
        global $pixdb, $datetime;
        $id = false;
        $id = $pixdb->getRow(
            'regions',
            ['name' => $region],
            'id'
        );

        $id ? $id = $id->id : false;

        if (
            !$id &&
            $region !== '' &&
            $nation
        ) {
            $id = $pixdb->insert(
                'regions',
                [
                    'nation' => $nation,
                    'name' => $region,
                    'createdAt' => $datetime
                ]
            );
        }

        return $id;
    }
    public function getStateId(
        $state,
        $region = false,
        $nation = null
    ) {
        global $pixdb, $datetime;
        $id = false;
        $id = $pixdb->getRow(
            'states',
            ['name' => $state],
            'id'
        );

        $id ? $id = $id->id : false;

        if (!$id && $referState = $this->getStateInfo($state)) {
            $id = $pixdb->getRow(
                'states',
                ['name' => $referState['name']],
                'id'
            );

            $id ? $id = $id->id : false;
        }

        if (
            !$id &&
            $region &&
            $state !== ''
        ) {
            $id = $pixdb->insert(
                'states',
                [
                    'nation' => $nation,
                    'region' => $region,
                    'name' => $state,
                    'createdAt' => $datetime
                ]
            );
        }

        if (!$id && $referState = $this->getStateInfo($state)) {
            if ($nation = $this->getNationId($referState['nation'])) {
                if ($region = $this->getRegionId($referState['region'])) {
                    $id = $pixdb->insert(
                        'states',
                        [
                            'nation' => $nation,
                            'region' => $region,
                            'name' => $referState['name'],
                            'createdAt' => $datetime
                        ]
                    );
                }
            }
        }

        return $id;
    }
    public function getChapterId(
        $chapter,
        $nation = null,
        $region = null,
        $state = false
    ) {
        global $pixdb, $datetime, $pix;
        $id = false;

        $qSearch = q("%$chapter%");
        if ($fetchChaptr = $pixdb->getRow(
            'chapters',
            ['#QRY' => "name like $qSearch"],
            'id'
        )) {
            $id = $fetchChaptr->id;
        } elseif (
            !$id &&
            $chapter !== '' &&
            $state
        ) {
            $id = $pixdb->insert(
                'chapters',
                [
                    'nation' => $nation,
                    'region' => $region,
                    'state' => $state,
                    'name' => $chapter,
                    'createdAt' => $datetime
                ]
            );
        } elseif (
            !$id &&
            $referSectn = $this->getSectionInfo($chapter)
        ) {
            if ($nation = $this->getNationId($referSectn['nation'])) {
                if ($region = $this->getRegionId($referSectn['region'])) {
                    if ($state = $this->getStateId($referSectn['state'])) {
                        $id = $pixdb->insert(
                            'chapters',
                            [
                                'nation' => $nation,
                                'region' => $region,
                                'state' => $state,
                                'name' => $referSectn['name'],
                                'createdAt' => $datetime
                            ]
                        );

                        if (!$id) {

                            $dir = $pix->basedir . 'chapter-err/';
                            if (!is_dir($dir)) {
                                mkdir($dir, 0755, true);
                            }
                            $name = $referSectn['name'];
                            $errLogNode = fopen($dir . date('Y-m-d-H') . '.txt', 'a+');
                            fwrite($errLogNode, "\n\r $chapter | $name \n");
                            fclose($errLogNode);
                        }
                    }
                }
            }
        }

        return $id;
    }
    public function getOccupationId($occupation)
    {
        global $pixdb;
        $id = false;
        $id = $pixdb->getRow(
            'occupation',
            ['name' => $occupation],
            'id'
        );

        $id ? $id = $id->id : false;

        if (!$id) {
            $id = $pixdb->insert(
                'occupation',
                ['name' => $occupation]
            );
        }
        return $id;
    }
    public function getIndustryId($industry)
    {
        global $pixdb;
        $id = false;
        $id = $pixdb->getRow(
            'industry',
            ['name' => $industry],
            'id'
        );

        $id ? $id = $id->id : false;

        if (!$id) {
            $id = $pixdb->insert(
                'industry',
                ['name' => $industry]
            );
        }

        return $id;
    }
    public function getProvider(
        $id,
        $fields = false
    ) {
        global $pixdb;
        return $pixdb->get(
            'benefit_providers',
            [
                'id' => $id,
                'single' => 1
            ],
            $fields ?:
                'id,
                name,
                address,
                website,
                email,
                cntryCode,
                phone,
                logo,
                status'
        );
    }
    public function getProviders(
        $ids,
        $fields = false
    ) {
        global $pixdb;
        return !empty($ids) ?
            $pixdb->fetchAssoc(
                'benefit_providers',
                ['id' => $ids],
                $fields ?:
                    'id, 
                name, 
                address,
                email, 
                cntryCode, 
                status'
            ) : [];
    }
    public function getCategory(
        $id,
        $fields = false
    ) {
        global $pixdb;
        return $pixdb->get(
            'categories',
            [
                'id' => $id,
                'single' => 1
            ],
            $fields ?:
                'id,
                ctryName,
                slug,
                type,
                itryIcon,
                enable'
        );
    }
    public function getCategories(
        $ids,
        $fields = false
    ) {
        global $pixdb;
        return !empty($ids) ?
            $pixdb->fetchAssoc(
                'categories',
                ['id' => $ids],
                $fields ?:
                    'id, 
                ctryName, 
                type, 
                itryIcon, 
                enable'
            ) : [];
    }
    public function getCareerTypes(
        $ids,
        $fields = false
    ) {
        global $pixdb;
        return !empty($ids) ?
            $pixdb->fetchAssoc(
                'career_types',
                ['id' => $ids],
                $fields ?:
                    'id, 
                name, 
                enabled'
            ) : [];
    }
    public function getCareerType(
        $id,
        $fields = false
    ) {
        global $pixdb;
        return $pixdb->get(
            'career_types',
            [
                'id' => $id,
                'single' => 1
            ],
            $fields ?:
                'id, 
            name, 
            enabled'
        );
    }
    public function getBenefitScopeName($key)
    {
        return self::benefitScopeNames[$key] ?? $key;
    }
    public function getBenefitStatusName($key)
    {
        return self::benefitStatusNames[$key] ?? $key;
    }
    public function getLocationsTypes($key)
    {
        return self::scopeTables[$key] ?? $key;
    }
    public function postNotification(
        $id,
        $from = null,
        $type,
        $title,
        $msg = null,
        $props = null
    ) {
        global $datetime, $pix;

        $wData = array(
            'from' => $from,
            'time' => $datetime,
            'title' => $title,
            'msg' => $msg,
            'type' => $type,
            'props' => $props
        );
        $ntdir = $pix->datas . 'notifications/';
        if (!is_dir($ntdir)) {
            mkdir($ntdir, 0755, true);
        }
        $nf = fopen($ntdir . $id . '.txt', 'a+');
        fwrite(
            $nf,
            json_encode(
                $wData
            ) . "\n\n\n\n===||===\n\n\n\n"
        );
        fclose($nf);

        return true;
    }
    public function loadNotification($id)
    {
        global $pix;
        $nLimit = 200;
        $nFile = $pix->datas . 'notifications/' . $id . '.txt';
        $nData = [];
        if (is_file($nFile)) {
            $nData = file_get_contents($nFile);
            if ($nData) {
                $nData = array_reverse(
                    array_map(
                        'trim',
                        explode('===||===', $nData)
                    )
                );
                $nData = array_filter($nData);

                // discarding old notifications
                $separator = "\n\n\n===||===\n\n\n";
                if (count($nData) > $nLimit) {
                    $nData = array_slice($nData, 0, $nLimit);
                    file_put_contents(
                        $nFile,
                        implode(
                            $separator,
                            array_reverse($nData)
                        ) . $separator
                    );
                }
                $nData = array_map('json_decode', $nData);
            }
        }
        if (is_array($nData)) {
            return $nData;
        } else {
            return [];
        }
    }
    public function getNotificationLink($nData)
    {
        global $isAnyAdmin;

        $link = 'javascript:;';
        switch ($nData->type ?? '') {
            case 'new-member':
                $link = ADMINURL . '?page=members&sec=details&id=' . ($nData->from ?? '');
                break;

            case 'new-membership':
                $link = ADMINURL . '?page=members&sec=details&id=' . ($nData->from ?? '');
                break;

            case 'new-payment':
                $link = ADMINURL . '?page=transactions&sec=details&id=' . ($nData->props->id ?? '');
                break;

            case 'new-enquiry':
                $link = ADMINURL . '?page=enquiries&sec=details&id=' . ($nData->props->enquiry ?? '');
                break;

            case 'advocacy-signed':
                $link = ADMINURL . '?page=advocacy&sec=details&id=' . ($nData->props->id ?? '');
                break;
        }
        return $link;
    }
    public function getAvatar($img, $size = '150x150')
    {
        if ($img) {
            global $pix;
            return DOMAIN . 'uploads/avatars/' . $pix->get_file_variation($img, $size);
        }
        return null;
    }
    public function removeMember($id)
    {
        global $pixdb, $pix;

        $member = $pixdb->getRow('members', ['id' => $id], 'avatar');
        if ($member && $member->avatar) {
            $pix->cleanThumb(
                'avatars',
                $pix->uploads . 'avatars/' . $member->avatar
            );
        }

        $advocacies = $pixdb->get(
            'member_advocacy',
            ['member' => $id],
            'signature, pdf'
        )->data;
        foreach ($advocacies as $adv) {
            // clean signatures
            if ($adv->signature) {
                $pix->cleanThumb(
                    'signature',
                    $pix->uploads . 'signatures/' . $adv->signature
                );
            }

            // clean pdfs
            if ($adv->pdf) {
                $pix->remove_file(
                    $pix->uploads . 'advocacy/' . $adv->pdf
                );
            }
        }

        $pixdb->delete('member_advocacy', ['member' => $id]);
        $pixdb->delete('memberships', ['member' => $id]);
        $pixdb->delete('members_auth', ['id' => $id]);
        $pixdb->delete('members_certification', ['member' => $id]);
        $pixdb->delete('members_education', ['member' => $id]);
        $pixdb->delete('members_expertise', ['member' => $id]);
        $pixdb->delete('members_info', ['member' => $id]);
        $pixdb->delete('members_referrals', ['#QRY' => "user=$id or refBy=$id"]);
        $pixdb->delete('members_switch', ['member' => $id]);
        $pixdb->delete('members_verification', ['member' => $id]);
        // $pixdb->delete('transactions', ['member' => $id]);
        $pixdb->delete('affiliate_leaders', ['mbrId' => $id]);
        $pixdb->delete('collegiate_leaders', ['mbrId' => $id]);
        $pixdb->delete('officers', ['memberId' => $id]);
        $pixdb->delete('paid_benefits', ['members' => $id]);
        $pixdb->delete('section_leaders', ['mbrId' => $id]);
        $pixdb->delete('state_leaders', ['mbrId' => $id]);
        $pixdb->delete('members', ['id' => $id]);
    }

    public function removeUnderLocations(
        $region = null,
        $state = null,
        $chapter = null
    ) {
        global $pixdb;

        $regIds = [];
        $regId = $pixdb->get(
            'regions',
            [
                'nation' => $region
            ],
            'id'
        )->data;
        foreach ($regId as $row) {
            $regIds[] = $row->id;
        }
        $stateIds = $state ? $state : $regIds;

        $stIds = [];
        $stId = $pixdb->get(
            'states',
            [
                'region' => $stateIds
            ],
            'id'
        )->data;
        foreach ($stId as $row) {
            $stIds[] = $row->id;
        }

        $chapterIds = $chapter ? $chapter : $stIds;

        if ($region) {
            $pixdb->delete(
                'regions',
                [
                    'nation' => $region
                ]
            );
        }
        if ($stateIds) {
            $pixdb->delete(
                'states',
                [
                    'region' => $stateIds
                ]
            );
        }

        if ($chapterIds) {
            $pixdb->delete(
                'chapters',
                [
                    'state' => $chapterIds
                ]
            );
        }
    }

    public function updateAdvocacyLocations($ids, $scope)
    {
        global $pixdb;

        $loctnIds = [];
        foreach ($ids as $id) {
            $loctnIds[] = 'locations like \'%"' . $id . '"%\'';
        }
        $locqry = '(' . implode(' OR ', $loctnIds) . ')';
        $locId = $pixdb->get(
            'advocacies',
            [
                'scope' => $scope,
                '#QRY' => $locqry
            ],
            'id, locations'
        );

        foreach ($locId->data as $loc) {
            $locIds = json_decode($loc->locations);
            $key = array_map(function ($ids) use ($locIds) {
                return array_search($ids, $locIds);
            }, $ids);
            $key = array_filter($key, function ($ky) {
                return $ky !== false;
            });
            foreach ($key as $ky) {
                unset($locIds[$ky]);
            }

            $locIds = array_values($locIds);
            $locIds = json_encode($locIds);
            $pixdb->update('advocacies', ['scope' => $scope, 'id' => $loc->id], ['locations' => $locIds]);
        }
    }

    public function removeNation($ids, $scope)
    {
        global $pixdb;

        $ids = is_array($ids) ? $ids : array($ids);

        $this->updateAdvocacyLocations($ids, $scope);
        $pixdb->update('members_info', ['nationId' => $ids], ['nationId' => null]);
        $this->removeUnderLocations($ids, '', '');
        $pixdb->delete('event_locations', ['nation' => $ids]);
        $pixdb->delete('nations', ['id' => $ids]);
    }

    public function removeRegion($ids, $scope)
    {
        global $pixdb;

        $ids = is_array($ids) ? $ids : array($ids);

        $this->updateAdvocacyLocations($ids, $scope);
        $pixdb->update('members_info', ['regionId' => $ids], ['regionId' => null]);
        $pixdb->update('event_locations', ['region' => $ids], ['region' => null, 'state' => null, 'chapter' => null]);
        $this->removeUnderLocations('', $ids, '');
        $pixdb->delete('regions', ['id' => $ids]);
    }

    public function removeState($ids, $scope)
    {
        global $pixdb;

        $ids = is_array($ids) ? $ids : array($ids);

        $this->updateAdvocacyLocations($ids, $scope);
        $pixdb->update('members_info', ['orgznSteId' => $ids], ['orgznSteId' => null]);
        $pixdb->update('event_locations', ['state' => $ids], ['state' => null, 'chapter' => null]);
        $this->removeUnderLocations('', '', $ids);
        $pixdb->delete('states', ['id' => $ids]);
    }



    public function removeChapter($ids, $scope)
    {
        global $pixdb;

        $ids = is_array($ids) ? $ids : array($ids);

        $this->updateAdvocacyLocations($ids, $scope);
        $pixdb->update('event_locations', ['chapter' => $ids], ['chapter' => null]);
        $pixdb->update('members_info', ['cruntChptr' => $ids], ['cruntChptr' => null]);
        $pixdb->update('members_info', ['chptrOfInitn' => $ids], ['chptrOfInitn' => null]);
        $pixdb->delete('chapters', ['id' => $ids]);
    }

    public function generateAuthToken()
    {
        global $pix;
        return $pix->makeString(20, 'uln') . '.' .
            $pix->makeString(120, 'uln') . '.' .
            $pix->makeString(30, 'uln') . '--' .
            $pix->makeString(41, 'uln') . '_' .
            $pix->makeString(12, 'uln');
    }
    public function getNews($scope)
    {
        global
            $datetime,
            $pixdb,
            $pix;

        if (!(
            $scope == 'national' ||
            $scope == 'regional' ||
            $scope == 'state' ||
            $scope == 'chapter'
        )) {
            return [];
        }

        $newsList = [];
        $cacheStatus = $pix->getData('news-cache-status');
        $expiry = $cacheStatus->{$scope}->expiry ?? 0;
        $dbCollectFlds = 'slug, title, imageUrl, description, provider, newsDate';

        if ($expiry >= time()) {
            $newsList = $pixdb->get(
                'news',
                [
                    'scope' => $scope,
                    '#SRT' => 'newsDate desc'
                ],
                $dbCollectFlds
            )->data;
            // 
        } else {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.bing.microsoft.com/v7.0/news/search?q=' . $scope . '&sortBy=Date&count=10&offset=1',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Ocp-Apim-Subscription-Key: 647abbc532684b5e9eed480c6502f0fe'
                ),
            ));

            $response = curl_exec($curl);
            curl_close($curl);

            $newsData = $response ? json_decode($response) : null;
            if (
                $newsData &&
                isset($newsData->value) &&
                is_array($newsData->value)
            ) {
                // fetch existing data
                $exNews = $pixdb->fetchAssoc('news', ['scope' => $scope], $dbCollectFlds, 'slug');

                $newsIns = $pixdb->prepareInsert(
                    'news',
                    'scope',
                    'createdAt',
                    'newsDate',
                    'title',
                    'slug',
                    'imageUrl',
                    'newsUrl',
                    'provider',
                    'description'
                );

                $delCheck = [];

                foreach ($newsData->value as $news) {
                    $gSlug = str2url($news->name ?? 'Untitled News ' . rand(0, 99999));
                    $delCheck[] = $gSlug;

                    if (isset($exNews[$gSlug])) {
                        $newsList[] = $exNews[$gSlug];
                        // 
                    } else {
                        $nwsTitle = $news->name ?? 'Untitled News';
                        $nwsImg = $news->image->thumbnail->contentUrl ?? null;
                        $nwsDesc = $news->description ?? null;
                        $nwsprovider = $news->provider[0]->name ?? null;
                        $nwsDate = date('Y-m-d H:i:s', strtotime($news->datePublished ?? '-1 day'));

                        $dbData = [
                            $scope,
                            $datetime,
                            $nwsDate,
                            $nwsTitle,
                            $gSlug,
                            $nwsImg,
                            $news->url ?? null,
                            $nwsprovider,
                            $nwsDesc
                        ];
                        if ($newsIns->execute($dbData)) {
                            $newsList[] = (object)[
                                'slug' => $gSlug,
                                'title' => $nwsTitle,
                                'imageUrl' => $nwsImg,
                                'provider' => $nwsprovider,
                                'newsDate' => $nwsDate,
                                'description' => $nwsDesc
                            ];
                        }
                    }
                }

                // removing old
                $remSlugs = array_diff(array_keys($exNews), $delCheck);
                if (!empty($remSlugs)) {
                    $pixdb->delete('news', ['slug' => $remSlugs]);
                }
            }

            if (!isset($cacheStatus->{$scope})) {
                $cacheStatus->{$scope} = (object)[];
            }
            $cacheStatus->{$scope}->expiry = time() + 21600;
            $pix->setData('news-cache-status', $cacheStatus);
        }

        return $newsList;
    }

    public function getAnyAdmins(
        $ids,
        $fields = false
    ) {
        global $pixdb;
        return !empty($ids) ?
            $pixdb->fetchAssoc(
                'admins',
                [
                    'id' => $ids
                ],
                $fields ?:
                    'id, name, type, username, email'
            ) : [];
    }

    public function getAnyAdmin(
        $id,
        $fields = false
    ) {
        global $pixdb;
        return $pixdb->getRow(
            'admins',
            ['id' => $id],
            $fields ?:
                'id, name, type, username, email'
        );
    }
    public function getStateInfo($name)
    {
        $name = strtoupper($name);
        return isset($this->states[$name]) ? $this->states[$name] : null;
    }
    public function getSectionInfo($name)
    {
        $name = str2url($name);
        return isset($this->sections[$name]) ? $this->sections[$name] : null;
    }
    public function getChapter(
        $id,
        $fields = false
    ) {
        global $pixdb;
        return $pixdb->get(
            'chapters',
            [
                'id' => $id,
                'single' => 1
            ],
            $fields ?:
                'id,
                nation,
                region,
                state,
                name,
                createdAt,
                enabled,
                updatedAt,
                members'
        );
    }

    public function getChapters(
        $ids = [],
        $fields = false
    ) {
        global $pixdb;

        $cnds = [
            '#SRT' => 'name ASC'
        ];
        if (!empty($ids)) {
            $cnds['id'] = $ids;
        }
        return $pixdb->fetchAssoc(
            'chapters',
            $cnds,
            $fields ?:
                'id,
                nation,
                region,
                state,
                name,
                createdAt,
                enabled,
                updatedAt,
                members'
        );
    }

    public function getAffiliation(
        $id,
        $fields = false
    ) {
        global $pixdb;
        return $pixdb->get(
            'affiliates',
            [
                'id' => $id,
                'single' => 1
            ],
            $fields ?:
                'id,
                name,
                createdAt,
                enabled,
                updatedAt'
        );
    }

    public function getAffiliations(
        $ids = [],
        $fields = false
    ) {
        global $pixdb;

        $cnds = [
            '#SRT' => 'name ASC'
        ];
        if (!empty($ids)) {
            $cnds['id'] = $ids;
        }
        return $pixdb->fetchAssoc(
            'affiliates',
            $cnds,
            $fields ?:
                'id,
                name,
                createdAt,
                enabled,
                updatedAt'
        );
    }

    public function getCollgueSection(
        $id,
        $fields = false
    ) {
        global $pixdb;
        return $pixdb->get(
            'collegiate_sections',
            [
                'id' => $id,
                'single' => 1
            ],
            $fields ?:
                'id,
                name,
                createdAt,
                enabled,
                updatedAt'
        );
    }

    public function getCollgueSections(
        $ids,
        $fields = false
    ) {
        global $pixdb;
        return !empty($ids) ?
            $pixdb->fetchAssoc(
                'collegiate_sections',
                ['id' => $ids],
                $fields ?:
                    'id,
                name,
                createdAt,
                enabled,
                updatedAt'
            ) : [];
    }

    public function calcOneYrExpiry()
    {
        $now = time();
        $currYr = date('Y');

        $yrEndDate = $currYr . '-' . FISCAL_YEAR_END;
        $sep30 = strtotime($yrEndDate);

        // If Sep 30 has passed, move to next year
        if ($now > $sep30) {
            $yrEndDate = ($currYr + 1) . '-' . FISCAL_YEAR_END;
            $sep30 = strtotime($yrEndDate);
        }

        return date('Y-m-d', $sep30);
    }
    public function calcInstlmntExpiry($start, $installment, $instllmntPhase)
    {
        if (!strtotime($start) || $installment <= 0 || $instllmntPhase <= 0) {
            return false;
        }
        $instllmntStart = date('Y-m-d', strtotime($start));
        $daysPerInstallment = floor(365 / $installment);
        $totalDays = $daysPerInstallment * $instllmntPhase;
        return date('Y-m-d', strtotime("+$totalDays days", strtotime($instllmntStart)));
    }
    public function createTxn($txnDta)
    {
        global $pix, $pixdb, $datetime;

        $txnId = null;
        $txnTbData = [];

        if (!is_object($txnDta)) {
            $txnDta = (object)$txnDta;
        }

        if (
            isset(
                $txnDta->type,
                $txnDta->member,
                $txnDta->amount,
                $txnDta->title,
                $txnDta->items
            )
        ) {
            $nTxnId = $pix->makeTxnId();

            $txnTbData = [
                'txnid' => $nTxnId,
                'type' => $txnDta->type,
                'method' => 'stripe',
                'member' => $txnDta->member,
                'date' => $datetime,
                'amount' => $txnDta->amount,
                'title' => $txnDta->title,
                'posDoneBy' => $txnDta->posDoneBy ?? null,
                'method' => $txnDta->paymentMode ?? 'stripe'
            ];

            $insId = $pixdb->insert(
                'transactions',
                $txnTbData
            );

            if ($insId) {
                $txnId = $nTxnId;
                $txnItmsTbData = [];
                if (is_array($txnDta->items)) {
                    foreach ($txnDta->items as $itm) {

                        $insertRow = [
                            $insId,
                            $itm->code,
                            $itm->sectionId != '' ? $itm->sectionId : null,
                            $itm->affiliateId != '' ? $itm->affiliateId : null,
                            $itm->details ? json_encode($itm->details) : null,
                            $itm->amount,
                            $itm->title ?? null,
                            $itm->beneficiary ?? NULL,
                            $itm->type ?? null,
                        ];

                        $txnItmsTbData[] = $insertRow;
                    }
                    if ($txnItmsTbData) {
                        $pixdb->multiInsert(
                            'txn_items',
                            ['txnId', 'pdtCode', 'sectionId', 'affiliateId', 'details', 'amount', 'title', 'benefitTo', 'type'],
                            $txnItmsTbData
                        );
                    }
                }
            }
        }

        return $txnId;
    }
    public function setLoginData($memberId)
    {
        return include $this->evgDir . 'login.php';
    }
    public function getDues($memberIds, $accountId = null)
    {
        return include $this->evgDir . 'dues-list.php';
    }
    public function rosterRenewalReq($list, $accountId = null, $leader = null, $stripeUser = null, $checkout = true)
    {
        return include $this->evgDir . 'roster-renewal-req.php';
    }
    public function getMemberInfo($ids, $membersTbFields = [], $memberInfoTbFields = [], $memberCircle = false)
    {
        global $pixdb;

        $memberInfo = [];
        if (!empty($ids)) {
            $fields = ['m.id'];
            foreach ($membersTbFields as $f) {
                $fields[] = "m.$f";
            }
            foreach ($memberInfoTbFields as $f) {
                $fields[] = "i.$f";
            }
            if ($memberCircle) {
                $fields[] = "i.cruntChptr";
                $fields[] = "i.collegiateSection";
            }
            $fields = implode(',', array_unique($fields));

            $memberInfo = $pixdb->fetchAssoc(
                [
                    ['members', 'm', 'id'],
                    ['members_info', 'i', 'member']
                ],
                [
                    'm.id' => $ids
                ],
                $fields,
                'id'
            );
            if ($memberCircle) {
                $mmbrAffiliatns = $pixdb->get(
                    'members_affiliation',
                    ['member' => $ids],
                    'member, affiliation'
                )->data;
                $affiliateIds = [];
                $mmbrAffil = [];
                foreach ($mmbrAffiliatns as $row) {
                    if (!isset($mmbrAffil[$row->member])) {
                        $mmbrAffil[$row->member] = [];
                    }
                    $affiliateIds[] = $row->affiliation;
                    $mmbrAffil[$row->member][] = $row->affiliation;
                }

                $sectionIds = collectObjData($memberInfo, 'cruntChptr');
                $sections = $this->getChapters($sectionIds, 'id, state, name');
                $affilateOrgzn = $this->getAffiliations($affiliateIds, 'id, name');
                $colSectnIds = collectObjData($memberInfo, 'collegiateSection');
                $collegiateSection = $this->getCollgueSections($colSectnIds, 'id, name');

                foreach ($memberInfo as $mbr) {
                    $mbr->section = null;
                    $mbr->affilate = null;
                    if (isset($mbr->cruntChptr, $sections[$mbr->cruntChptr])) {
                        $mbr->section = $sections[$mbr->cruntChptr];
                    }
                    if (isset($mmbrAffil[$mbr->id])) {
                        $mbr->affilate = [];
                        foreach ($mmbrAffil[$mbr->id] as $affilateId) {
                            if (isset($affilateOrgzn[$affilateId])) {
                                $mbr->affilate[] = $affilateOrgzn[$affilateId];
                            }
                        }
                    }
                    if (isset($mbr->collegiateSection, $collegiateSection[$mbr->collegiateSection])) {
                        $mbr->collegSection = $collegiateSection[$mbr->collegiateSection];
                    }
                }
            }
        }
        return $memberInfo;
    }
    public function isMemberLeader($member, $leader)
    {
        $leaderSection = isset($leader->sections) ? $leader->sections : [];
        $leaderAffiliates = isset($leader->affiliates) ? $leader->affiliates : [];
        $leaderColgSectn = isset($leader->colgSectns) ? $leader->colgSectns : [];

        if (isset($member->cruntChptr) && in_array($member->cruntChptr, $leaderSection)) {
            return true;
        }
        if (isset($member->affilateOrgzn) && in_array($member->affilateOrgzn, $leaderAffiliates)) {
            return true;
        }
        if (isset($member->collegiateSection) && in_array($member->collegiateSection, $leaderColgSectn)) {
            return true;
        }
        return false;
    }
    public function addMember($args)
    {
        return include $this->evgDir . 'add-member.php';
    }
    public function updateMember($args)
    {
        return include $this->evgDir . 'update-member.php';
    }
    public function dollarInCents($amount)
    {
        return $amount * 100;
    }
    public function getLeaderCircles($leaderId, $userRoles)
    {
        global $pixdb;
        $sections = [];
        $affiliations = [];
        $collegiates = [];

        $isStateLeader = in_array('state-leader', $userRoles);
        $isSectionLeader = in_array('section-leader', $userRoles);
        $isSectionPresident = in_array('section-president', $userRoles);
        $isAffiliateLeader = in_array('affiliate-leader', $userRoles);
        $isCollegiateLeader = in_array('collegiate-leaders', $userRoles);
        $isOfficer = in_array('section-officer', $userRoles);

        $sections = [];
        if ($isStateLeader) {
            $sections = $pixdb->getCol(
                [
                    ['chapters', 'c', 'state'],
                    ['state_leaders', 's', 'stateId']
                ],
                [
                    's.mbrId' => $leaderId
                ],
                'c.id',
                'id'
            );
        }
        if ($isSectionLeader || $isSectionPresident) {
            $getSections = $pixdb->getCol(
                'section_leaders',
                [
                    'mbrId' => $leaderId
                ],
                'secId'
            );
            $sections = array_unique(array_merge($sections, $getSections));
        }
        if ($isAffiliateLeader) {
            $affiliations = $pixdb->getCol(
                'affiliate_leaders',
                [
                    'mbrId' => $leaderId
                ],
                'affId'
            );
        }
        if ($isCollegiateLeader) {
            $collegiates = $pixdb->getCol(
                'collegiate_leaders',
                [
                    'mbrId' => $leaderId
                ],
                'coliId'
            );
        }
        if ($isOfficer) {
            $circles = $pixdb->get(
                'officers',
                ['memberId' => $leaderId],
                'circle,circleId'
            );
            foreach ($circles as $row) {
                if ($row->circle == 'section') {
                    $sections[] = $row->circleId;
                }
                if ($row->circle == 'affiliate') {
                    $affiliations[] = $row->circleId;
                }
                if ($row->circle == 'collegiate') {
                    $collegiates[] = $row->circleId;
                }
            }
        }
        return (object)[
            'sections' => $sections,
            'affiliates' => $affiliations,
            'colgSectns' => $collegiates
        ];
    }
    public function changeAccessToken($memberId)
    {
        global $pixdb;

        $token = $this->generateAuthToken();
        if ($token) {
            $pixdb->update(
                'members_auth',
                ['id' => $memberId],
                ['token' => $token]
            );
        }
    }
    //get officers title id
    public function getOfficerTitleId($title)
    {
        global $pixdb;
        $titleId = $pixdb->getRow(
            'officers_titles',
            ['title' => $title],
            'id'
        )->id;
        if (!$titleId) {
            $titleId = $pixdb->insert(
                'officers_titles',
                ['title' => $title]
            );
        }
        return $titleId;
    }
    //get affiliates id
    public function getAffiliatesId($title)
    {
        global $pixdb;
        $affId = null;
        $affiliate = $pixdb->getRow(
            'affiliates',
            ['name' => $title],
            'id'
        );
        if ($affiliate) {
            $affId = $affiliate->id;
        }
        if (!$affId) {
            $affId = $pixdb->insert(
                'affiliates',
                ['name' => $title]
            );
        }
        return $affId;
    }

    public function getSelForFilter(
        $ids = [],
        $table = false
    ) {
        global $pix, $pixdb;
        // $selInfo = false;
        $tmp = [];

        if (array_key_exists($table, self::tables)) {
            $conds = [
                '#SRT' => 'id asc'
            ];
            if (!empty($ids)) {
                $conds['id'] = $ids;
            }
            $selInfo = $pixdb->fetchAssoc(
                $table,
                $conds
            );

            foreach ($selInfo as $row) {
                $obj = (object)[
                    'id'   => $row->id,
                    'name' => $row->name ?? $row->title ?? null
                ];

                $tmp[] = $obj;
            }
        }

        return $tmp;
    }
}
$evg = new EverGreen();

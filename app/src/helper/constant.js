export const pl = process.env.REACT_APP_ENV;
export const BASE_URL = process.env.REACT_APP_API_URL;
export const ISLOCAL = !!window.localStorage.isLocal;

// export const BASE_URL = 1 ?
//     'http://localhost/evergreen/api' :
//     'https://memberapp-api.apps.openxcell.dev/api';

export const SECONDARY_COLOR = "#C33FD9";
// export const SECONDARY_COLOR = '#c33ed7'
// export const DARK_RED = '#a20404'
// export const HEADER_COLOR = '#E10001'
export const HEADER_COLOR = "#5b2166";
// export const HEADER_COLOR = '#153BF6'
export const FOOTER_COLOR = "#6f0404";
// export const FOOTER_COLOR = '#020D40'
export const SITE_NAME = "NCNW";
export const SITE_SHORT_DESC = "CULTIVATE MEMBERSHIP GROWTH";
export const BUTTON_COLOR = "#E10001";
// export const BUTTON_COLOR = '#34BEBF'
export const WEBSITE_URL = process.env.REACT_APP_WEBSITE_URL;
// export const WEBSITE_URL = process.env.REACT_APP_WEBSITE_URL;
export const APP_STORE_LINK =
  "https://apps.apple.com/us/app/evergreen-member-management/id1532083835";

export const GOOGLE_MAP_KEY = "AIzaSyBDfLUrq9BAh_3fr8PKlSf2OE1tNC26kA0";

export const PAGE_ID = {
  aboutUs: 1,
  help: 2,
  privacyPolicy: 3,
  terms: 4,
  contactus: 5,
};

export const REGISTER_TYPE = {
  normal: "Normal",
  google: "Google",
  facebook: "Facebook",
};

// This navbar will be added in main header after successful login
// Path is used to highlight the active link/page

export const LOGIN_HEADER = [
  {
    label: "Home",
    path: "/home",
  },
  //   {
  //     label: "Benefits",
  //     path: "/benefits",
  //   },
  {
    label: "Dues",
    path: "/dues",
  },
  // {
  //     label: "Referrals",
  //     path: "/referrals",
  // },
  {
    label: "Resources",
    path: "/resources",
  },
  // {
  //     label: "Inbox",
  //     path: "/inbox",
  // },
  {
    label: "Advocacy",
    path: "/advocacy",
  },
  /* {
    label: "Programs",
    path: "/programs",
  }, */
  {
    label: "Events",
    path: "/events",
  },
  // {
  //   label: "Careers",
  //   path: "/careers",
  // },
  // {
  //     label: "Store",
  //     path: "/store",
  // },
  // {
  //   label: 'About Us',
  //   path: '/about_us',
  // },
];

export const TYPE_ID = {
  prefix: 1,
  suffix: 2,
  industry: 3,
  occupation: 4,
  university: 5,
  degree: 6,
  certification: 7,
  expertise: 8,
  salaryRange: 9,
  houseHold: 10,
  phoneCode: 11,
  country: 12,
};

export const PROFILE_OPTIONS = {
  prefix: [
    { label: "Mr.", value: 2933 },
    { label: "Mrs.", value: 2934 },
    { label: "Dr.", value: 2935 },
    { label: "Ms.", value: 2936 },
    { label: "Ambassador", value: 3475 },
    { label: "Atty.", value: 3476 },
    { label: "Bishop", value: 3477 },
    { label: "Commissioner", value: 3478 },
    { label: "Elder", value: 3479 },
    { label: "Judge", value: 3480 },
    { label: "Mayor", value: 3481 },
    { label: "Min.", value: 3482 },
    { label: "Miss", value: 3483 },
    { label: "Pastor", value: 3484 },
    { label: "President", value: 3485 },
    { label: "Representative", value: 3486 },
    { label: "Rev.", value: 3487 },
    { label: "Rev. Dr.", value: 3488 },
    { label: "Senator", value: 3489 },
    { label: "The Honorable", value: 3490 },
  ],
  suffix: [
    { label: "Jr.", value: 2937 },
    { label: "Sr.", value: 2938 },
    { label: "II", value: 2939 },
    { label: "III", value: 2940 },
    { label: "IV", value: 3474 },
  ],
  degree: [
    { label: "Less than high school", value: 3492 },
    { label: "High school diploma or equivalent", value: 3493 },
    { label: "Some college", value: 3494 },
    { label: "Associate's degree", value: 2941 },
    { label: "Bachelor's degree", value: 2942 },
    { label: "Master's degree", value: 2943 },
    { label: "Professional degree", value: 2945 },
    { label: "Doctoral Degree", value: 2944 },
  ],
  expertise: [
    { label: "Analytical", value: 2959 },
    { label: "Communication", value: 2960 },
    { label: "Computer", value: 2961 },
    { label: "Conceptual", value: 2962 },
    { label: "Core Competencies", value: 2963 },
    { label: "Creative Thinking", value: 2964 },
    { label: "Critical Thinking", value: 2965 },
  ],
  salaryRange: [
    { label: "No income", value: 2966 },
    { label: "0 - $40,000", value: 2967 },
    { label: "$40,001 to $80,000", value: 2968 },
    { label: "$80,001 to $120,000", value: 2969 },
    { label: "$120,001 to $160,000", value: 2970 },
    { label: "$160,001 to $200,000", value: 3471 },
    { label: "$200,000 to $240,000", value: 3472 },
    { label: "$240,000+", value: 3473 },
  ],
  houseHold: [
    { label: "Single", value: 2971 },
    { label: "Single with children", value: 2972 },
    { label: "Married", value: 2973 },
    { label: "Married with children", value: 2974 },
    { label: "Widowed", value: 3469 },
    { label: "Widowed with children", value: 3470 },
    { label: "Never Married", value: 3495 },
    { label: "Separated", value: 3496 },
    { label: "Divorced", value: 3497 },
  ],
  racialIdentity: [
    { label: "American Indian or Alaska Native", value: 3498 },
    { label: "Asian", value: 3499 },
    { label: "Black or African American", value: 3500 },
    { label: "Hispanic, Latino, or Spanish Origin", value: 3501 },
    { label: "Middle Eastern or North African", value: 3502 },
    { label: "Native Hawaiian or Other Pacific Islander", value: 3503 },
    { label: "White", value: 3504 },
    // { label: "Other (please specify)", value: null },
  ],
  employmentStatus: [
    { label: "Employed full-time", value: 3506 },
    { label: "Employed part-time", value: 3507 },
    { label: "Self-employed", value: 3508 },
    { label: "Unemployed", value: 3509 },
  ],
  volunteerInterest: [
    { label: "Health Equity", value: 3510 },
    { label: "Education", value: 3511 },
    { label: "Social Justice", value: 3512 },
    { label: "Economic Empowerment", value: 3513 },
    { label: "Mentorship", value: 3514 },
  ],

  memberRole: [
    { label: "State Leader", value: "state-leader" },
    { label: "Section Leader", value: "section-leader" },
    { label: "Section President", value: "section-president" },
    { label: "Affiliate Leader", value: "affiliate-leader" },
    { label: "Collegiate leaders", value: "collegiate-leaders" },
  ],
};

export const MONTH = [
  "JAN",
  "FEB",
  "MAR",
  "APR",
  "MAY",
  "JUN",
  "JUL",
  "AUG",
  "SEP",
  "OCT",
  "NOV",
  "DEC",
];

// DO not change the order of 'all' element in the array. Make it the 1st element
export const BENEFIT_LOCATION = [
  {
    label: "All",
    value: "AllBenefits",
  },
  {
    label: "National",
    value: "National",
  },
  {
    label: "Regional",
    value: "Regional",
  },
  {
    label: "State",
    value: "State",
  },
  {
    label: "Section",
    value: "Section",
  },
];

export const EVENT_LOCATION = [
  {
    label: "All",
    value: "AllEvents",
  },
  {
    label: "National",
    value: "National",
  },
  {
    label: "Regional",
    value: "Regional",
  },
  {
    label: "State",
    value: "State",
  },
  {
    label: "Section",
    value: "Section",
  },
];

export const NOTIFICATIONS_OPTIONS = [
  {
    label: 'NCNW Committees',
    options: [
      {
        key: 'yaca-youth',
        label: 'YACA/Youth '
      }
    ]
  },
  {
    label: 'Programs',
    options: [
      {
        key: 'bhcp',
        label: 'BHCP'
      },
      {
        key: 'girlcon',
        label: 'GirlCon'
      },
      {
        key: 'rise',
        label: 'RISE'
      },
      {
        key: 'ghwins',
        label: 'GHWINs'
      },
      {
        key: 'colgate',
        label: 'Colgate'
      }
    ]
  },
  {
    label: 'NCNW Departments',
    options: [
      {
        key: 'advocacy-policy',
        label: 'Advocacy & Policy'
      },
      {
        key: 'programs',
        label: 'Programs'
      }
    ]
  },
  {
    label: 'Communication Preferences',
    options: [
      {
        key: 'email',
        label: 'Email'
      },
      {
        key: 'text',
        label: 'Text'
      },
      {
        key: 'social-media',
        label: 'Social Media'
      }
    ]
  }
];

export const CATEGORIES_LINK = [
  {
    label: "RISE Project",
    link: "https://ncnw.org/project/rise/",
    categories: ["AllPrograms", "EconomicEmpowerment"]
  },
  {
    label: "Good Health Wins",
    link: "https://goodhealthwins.org/",
    categories: ["AllPrograms", "HealthEquity"]
  },
  {
    label: "GoodHealthWINs Communication Kit",
    link: "https://drive.google.com/drive/folders/1k1MHhj7cBUN1bX7wXbHHDHlf12OBk0bL",
    categories: ["AllPrograms", "HealthEquity"]
  },
  {
    label: "Colgate Bright Smiles Bright Futures",
    link: "https://ncnw.org/project/colgate-bright-smiles-bright-futures/",
    categories: ["AllPrograms", "HealthEquity"]
  },
  {
    label: "Monthly NCNW Sections & Chapters Reporting Form",
    link: "https://app.smartsheet.com/b/form/ce4ecd252d1140cf9d12fe8898a29d7e",
    categories: ["AllPrograms", "HealthEquity"]
  },
  {
    label: "National Reporting Tool",
    link: "https://app.smartsheet.com/b/form/9c341174029044778f05552dd52c772a",
    categories: ["AllPrograms", "HealthEquity", "STEAM", "EconomicEmpowerment", "SocialJustice"]
  }
];

export const PROGRAMS_CATEGORIES = [
  {
    label: "All",
    value: "AllPrograms"
  },
  {
    label: "Health Equity",
    value: "HealthEquity"
  },
  {
    label: "STEAM",
    value: "STEAM"
  },
  {
    label: "Economic Empowerment",
    value: "EconomicEmpowerment"
  },
  {
    label: "Social Justice",
    value: "SocialJustice"
  }
];

export const ADVOCACY_LOCATION = [
  {
    label: "All",
    value: "AllAdvocacy",
  },
  {
    label: "National",
    value: "National",
  },
  {
    label: "Regional",
    value: "Regional",
  },
  {
    label: "State",
    value: "State",
  },
  {
    label: "Section",
    value: "Section",
  },
];

export const NEWS_LOCATION = [
  {
    label: "National",
    value: "National",
  },
  {
    label: "Regional",
    value: "Regional",
  },
  {
    label: "State",
    value: "State",
  },
  {
    label: "Section",
    value: "Section",
  },
];

export const MEMBERSHIP_FOR = [
  {
    label: "Myself",
    value: "myself",
  },
  {
    label: "Purchase/Gift to Other",
    value: "gift",
  },
];

export const MEMBER_ROLES = [
  {
    label: "President",
    value: "president",
  },
  {
    label: "Vice President",
    value: "vice-president",
  },
];

// export const DONATE_SPACIFICATIONS = [
//   {
//     label: "In Honor Of",
//     value: "in-honor-of",
//   },
//   {
//     label: "In Memory Of",
//     value: "in-memory-of",
//   },
// ];

export const RECOMMENDATION_LETTER =
  BASE_URL + "/member/?method=recommendation-letter";

export const WEPAY_APP_ID = "739963";
export const WEPAY_VERSION = "3.0";
export const WEPAY_ENV = process.env.REACT_APP_WEPAY_ENV;
export const COUNTRY_CODE = [
  {
    label: "United States",
    phoneCode: "+1",
    countryCode: "US",
  },
  {
    label: "Canada",
    phoneCode: "+1",
    countryCode: "CA",
  },
];

export function getApiURL() {
  return "http://localhost/evergreen/api";
}

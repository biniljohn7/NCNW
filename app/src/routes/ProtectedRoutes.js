// import Home from "../components/home/Home";
// import Setting from '../components/settingPage/Setting'
// import BenefitCategory from "../components/benefit/BenefitCategory";
import BenefitList from "../components/benefit/Benefit";
import Benefit from "../components/benefit/BenefitDetails";
import SearchBenefit from "../components/benefit/SearchBenefit";
import Career from "../components/career/Career";
import CareerDetail from "../components/career/CareerDetail";
// import Referral from "../components/referral/Referral";
import Resource from "../components/resource/Resource";
import Store from "../components/store/Store";
import Event from "../components/eventModule/Event";
import EventDetail from "../components/eventModule/EventDetail";
import Advocacy from "../components/advocacy/Advocacy";
import Programs from "../components/programs/Programs";
import AdvocacyDetail from "../components/advocacy/AdvocacyDetail";
import Dues from "../components/dues/Dues";
import Membership from "../components/dues/Membership";
import Setting from "../components/settingPage/Setting";
import News from "../components/news/News";
import NewsDetails from "../components/news/NewsDetail";
import Inbox from "../components/inbox/Inbox";
import NewPage from "../components/newPage/NewPage";
import Unauthorized from "../views/Unauthorized";
import LeaderDashboard from "../components/leader-dashboard/LeaderDashboard";

const protectedRoutes = [
    {
        path: "/home",
        component: NewPage,
    },
    // Do not include it in ProtectedLayout to avoid infinite loop
    {
        path: '/account',
        component: Setting,
    },
    // {
    //     path: "/benefits",
    //     component: BenefitCategory,
    // },
    {
        path: "/benefits/search/list",
        component: SearchBenefit,
    },
    {
        path: "/benefits/:name",
        component: BenefitList,
    },
    {
        path: "/benefits/:name/:categoryId/:benefitId",
        component: Benefit,
    },
    {
        path: "/careers",
        component: Career,
    },
    {
        path: "/careers/:title",
        component: CareerDetail,
    },
    // {
    //   path: '/referrals',
    //   component: Referral,
    // },
    {
        path: "/resources",
        component: Resource,
    },
    {
        path: "/store",
        component: Store,
    },
    {
        path: "/events",
        component: Event,
    },
    {
        path: "/events/:eventId",
        component: EventDetail,
    },
    {
        path: "/events/:name",
        component: EventDetail,
    },
    {
        path: "/advocacy",
        component: Advocacy,
    },
    {
        path: "/programs",
        component: Programs,
    },
    {
        path: "/advocacy/:advocacyType/:advocacyId",
        component: AdvocacyDetail,
    },
    {
        path: "/programs/:advocacyType/:advocacyId",
        component: AdvocacyDetail,
    },
    {
        path: "/dues",
        component: Dues,
    },
    {
        path: "/dues/membership",
        component: Membership,
        // component: Memberships,
    },
    {
        path: "/news",
        component: News,
    },
    {
        path: "/news/:newsfeedId",
        component: NewsDetails,
    },
    {
        path: "/inbox",
        component: Inbox,
    },
    {
        path: "/newLanding",
        component: NewPage,
    },
    {
        path: "/unauthorized",
        component: Unauthorized,
    },
    {
        path: "/leader-dashboard",
        component: LeaderDashboard,
        allowedRoles: ['state-leader', 'section-leader', 'section-president', 'affiliate-leader', 'collegiate-leaders', 'section-officer']
    }
];

export default protectedRoutes;

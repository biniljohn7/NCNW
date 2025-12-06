<?php
class pageHead
{
    public $pageTitle = '';
    public $titleBase = 'NCNW';
    public $titleRules = array(
        'page=login' => 'Login',
        'page=forgot-password&sec=request-status' => 'Request Status',
        'page=forgot-password' => 'Forgot Password',
        'page=account-verify&sec=pwd-reset&t=' => 'New Password',

        'page=benefits&sec=details' => 'Benefit Details',
        'page=benefits&sec=mod&id=new' => 'Create Benefit',
        'page=benefits&sec=mod' => 'Edit Benefit',
        'page=benefits' => 'Benefits',

        'page=provider&sec=details' => 'Provider Details',
        'page=provider&sec=mod&id=new' => 'Create Provider',
        'page=provider&sec=mod' => 'Edit Provider',
        'page=provider' => 'Providers',

        'page=ctry-benefit&sec=mod&id=new' => 'Create - Benefit Category',
        'page=ctry-benefit&sec=mod' => 'Edit - Benefit Category',
        'page=ctry-benefit' => 'Benefit Categories',

        'page=events&sec=details' => 'Event Details',
        'page=events&sec=mod&id=' => 'Edit Event',
        'page=events&sec=mod' => 'Create Event',
        'page=events' => 'Events',

        'page=ctry-event&sec=mod&id=new' => 'Create - Event Category',
        'page=ctry-event&sec=mod' => 'Edit - Event Category',
        'page=ctry-event' => 'Event Categories',

        'page=nation&sec=mod&id=new' => 'Create Country',
        'page=nation&sec=mod' => 'Edit Country',
        'page=nation' => 'Countries',

        'page=region&sec=mod&id=new' => 'Create Region',
        'page=region&sec=mod' => 'Edit Region',
        'page=region' => 'Regions',

        'page=state&sec=mod&id=new' => 'Create State',
        'page=state&sec=mod' => 'Edit State',
        'page=state&sec=details' => 'State Details',
        'page=state' => 'States',

        'page=chapter&sec=mod&id=new' => 'Create Section',
        'page=chapter&sec=mod' => 'Edit Section',
        'page=chapter' => 'Sections',
        'page=affiliates' => 'Affiliates',
        'page=collegiate-sections' => 'Collegiate Sections',

        'page=members&sec=details' => 'Member Details',
        'page=members' => 'Members',

        'page=transactions&sec=details' => 'Transactions Details',
        'page=transactions' => 'Transactions',

        'page=career&sec=details' => 'Career Details',
        'page=career&sec=mod&id=new' => 'Create Career',
        'page=career&sec=mod' => 'Edit Career',
        'page=career' => 'Careers',

        'page=cr-tags&sec=mod&id=new' => 'Create - Career Tag',
        'page=cr-tags&sec=mod' => 'Edit - Career Tag',
        'page=cr-tags' => 'Career Tags',

        'page=advocacy&sec=details' => 'Advocacy Details',
        'page=advocacy&sec=mod&id=new' => 'Create Advocacy',
        'page=advocacy&sec=mod' => 'Edit Advocacy',
        'page=advocacy' => 'Advocacies',

        'page=cms&sec=details' => 'CMS Details',
        'page=cms&sec=mod&id=new' => 'Create CMS',
        'page=cms&sec=mod' => 'Edit CMS',
        'page=cms' => 'CMS',

        'page=enquiries&sec=details' => 'Enquiries Details',
        'page=enquiries' => 'Enquiries',

        'page=enq-ques&sec=mod&id=new' => 'Create Question',
        'page=enq-ques&sec=mod' => 'Edit Question',
        'page=enq-ques' => 'Questions',

        'page=sub-admins&sec=details' => 'Sub Admin Details',
        'page=sub-admins&sec=mod&id=' => 'Edit Sub Admin',
        'page=sub-admins&sec=mod' => 'Create Sub Admin',
        'page=sub-admins' => 'Sub Admins',

        'page=member-packages&sec=mod-plan&type=[NUM]&id=new' => 'Create Membership charges',
        'page=member-packages&sec=mod-plan&type=' => 'Edit Membership charges',
        'page=member-packages&sec=details' => 'Membership Plan Details',
        'page=member-packages&sec=mod-type&id=new' => 'Create Membership Plan',
        'page=member-packages&sec=mod-type&id=[NUM]' => 'Edit Membership Plan',
        'page=member-packages' => 'Membership Plans',

        'evergreenadmin/' => 'Dashboard',
    );

    public function __construct()
    {
        $this->pageTitle = $this->titleBase;
        $qString = $_SERVER['REQUEST_URI'];
        $qString = preg_replace('/\d+/', '[NUM]', $qString);

        foreach ($this->titleRules as $exp => $title) {
            if (strpos($qString, $exp) !== false) {
                $this->pageTitle = $title . ' - ' . $this->titleBase;
                break;
            }
        }
    }
}
$pgHead = new pageHead();

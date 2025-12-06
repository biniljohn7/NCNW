<?php
class Mailgun
{
    public $domain = null;
    public $apikey = null;
    public function __construct()
    {
        $this->domain = $_ENV['MAILGUN_DOMAIN'] ?? '';
        $this->apikey = $_ENV['MAILGUN_APIKEY'] ?? '';
    }
    public function send(
        $to,
        $subject,
        $content
    ) {
        $r = (object)[
            'sent' => false
        ];
        $curl = curl_init();
        curl_setopt_array(
            $curl,
            [
                CURLOPT_URL => 'https://api.mailgun.net/v3/' . $this->domain . '/messages',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => [
                    'from' => 'Mailgun Sandbox <postmaster@sandboxfe19955942344cb0b99012df8c545dcf.mailgun.org>',
                    'to' => $to,
                    'subject' => $subject,
                    'html' => $content
                ],
                CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
                CURLOPT_USERPWD => "api:$this->apikey"
            ]
        );

        $response = curl_exec($curl);
        curl_close($curl);

        if ($response) {
            $response = json_decode($response);
            if ($qid = $response->id ?? 0) {
                $r->sent = true;
                $r->queueId = $qid;
            }
        }

        return $r;
    }
}

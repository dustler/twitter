<?php

use Twitter\Api;

class TwitterTest extends PHPUnit_Framework_TestCase {

    public function testConnect() {
        $api = new Api();
        $api->consumerKey = 'InbRMKMKnOlE51mwTLPzxkUck';
        $api->consumerSecret = 'WUDvfZB22tYykKg9dVGhmvZAKDWrnSveQSlgGE1lmZLXH4H1FV';
        $api->oAuthToken = '477071837-9SzUZo6wTpp9ti157O29OrLJE1Pdw78XYINoNgkA';
        $api->oAuthSecret = 'fFhZivjgFpEUq52ogq5ot7fx5ebmAvdj0zb3KAiPMhO0M';
        $api->init();
        $api->message('imf4wsh', 'bla bla bla');
    }
}

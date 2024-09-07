<?php

namespace Tests\Browser;

use Tests\TestCase;

class MobilePageVisualTest extends TestCase
{
    public function test_the_about_page_on_mobile_did_not_change() {
        $this->assertMobilePageMatchesSnapshot('/about');
    }

}

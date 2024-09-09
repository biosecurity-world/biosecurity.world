<?php

namespace Tests\Browser;

use Tests\TestCase;

class DesktopPageVisualTest extends TestCase
{
    public function test_the_about_page_did_not_change()
    {
        $this->assertDesktopPageMatchesSnapshot('/about');
    }

    public function test_the_terms_and_conditions_page_did_not_change()
    {
        $this->assertDesktopPageMatchesSnapshot('/legal/terms-of-service');

    }

    public function test_the_privacy_policy_page_did_not_change()
    {
        $this->assertDesktopPageMatchesSnapshot('/legal/privacy-policy');
    }
}

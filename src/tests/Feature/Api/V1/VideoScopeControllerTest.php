<?php

namespace Tests\Feature\Api\V1;

use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VideoScopeControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testCanMakeVideosPublic() {}
    public function testCanMakeVideosPrivate() {}
    public function testCanMakeVideosUnlisted() {}
}

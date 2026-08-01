<?php

it('returns a successful redirect to report page from root', function () {
    $response = $this->get('/');

    $response->assertRedirect('/report');
});

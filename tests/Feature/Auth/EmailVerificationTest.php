<?php

use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;

test('email verification screen can be rendered', function () {
    $this->markTestSkipped('Email verification is disabled because email is optional.');
});

test('email can be verified', function () {
    $this->markTestSkipped('Email verification is disabled because email is optional.');
});

test('email is not verified with invalid hash', function () {
    $this->markTestSkipped('Email verification is disabled because email is optional.');
});

test('already verified user visiting verification link is redirected without firing event again', function () {
    $this->markTestSkipped('Email verification is disabled because email is optional.');
});
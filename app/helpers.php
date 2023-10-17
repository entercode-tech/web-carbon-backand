<?php

// Generate UUID With Time Stamp
function generateUuid()
{
    $uuid = time() * 1000;
    return $uuid;
}

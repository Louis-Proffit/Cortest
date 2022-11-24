<?php

namespace App;

function list_files(string $dir)
{
    return array_diff(scandir($dir), array(".", ".."));
}

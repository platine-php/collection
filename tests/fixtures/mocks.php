<?php

declare(strict_types=1);

namespace Platine\Collection;

$mock_usort_to_false = false;

function usort(array &$array, callable $callback)
{
    global $mock_usort_to_false;
    if ($mock_usort_to_false) {
        return false;
    } else {
        return \usort($array, $callback);
    }
}

namespace Platine\Collection\Generic;

$mock_usort_to_false = false;

function usort(array &$array, callable $callback)
{
    global $mock_usort_to_false;
    if ($mock_usort_to_false) {
        return false;
    } else {
        return \usort($array, $callback);
    }
}

namespace Platine\Collection\Map;

$mock_uasort_to_false = false;
$mock_json_encode_to_false = false;

function uasort(array &$array, callable $callback)
{
    global $mock_uasort_to_false;
    if ($mock_uasort_to_false) {
        return false;
    } else {
        return \uasort($array, $callback);
    }
}

function json_encode($value, int $flags = 0, int $depth = 512)
{
    global $mock_json_encode_to_false;
    if ($mock_json_encode_to_false) {
        return false;
    } else {
        return \json_encode($value, $flags, $depth);
    }
}

<?php
$str = 'myNameIsAndrew';

$result = preg_match_all('/^([a-z]+)|([A-Z][a-z]+)/', $str, $m);

var_dump(strtolower(implode($m[0], ' ')));

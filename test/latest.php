<?php

require_once '../lib/include.php';
require_once 'info.php';

$run_info = $n->latest();

print_r($run_info);

$run_data = $n->activity($run_info[runId]);

echo '<br/><br/>';
print_r($run_data->activity->tags->note);

echo '<br/><br/>';
print_r($run_data->activity->geo->waypoints);

?>

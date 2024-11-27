<?php
function getCpuUsage() {
    // Read the /proc/stat file
    $stats1 = file('/proc/stat');
    $cpuLine1 = $stats1[0]; // The first line contains CPU stats
    // Extract numeric values from the line
    $values1 = array_map('intval', preg_split('/\s+/', trim($cpuLine1)));
    list($cpu, $user1, $nice1, $system1, $idle1) = array_slice($values1, 0, 5);

    // Sleep for 0.5 second to measure CPU usage
    usleep(500000);

    // Read the /proc/stat file again
    $stats2 = file('/proc/stat');
    $cpuLine2 = $stats2[0]; // The first line contains CPU stats
    // Extract numeric values from the line
    $values2 = array_map('intval', preg_split('/\s+/', trim($cpuLine2)));
    list($cpu, $user2, $nice2, $system2, $idle2) = array_slice($values2, 0, 5);

    // Calculate the differences
    $total1 = $user1 + $nice1 + $system1 + $idle1;
    $total2 = $user2 + $nice2 + $system2 + $idle2;
    if ($total2 === $total1) {
        // Avoid division by zero
        return 0;
    }
    $idleDiff = $idle2 - $idle1;
    $totalDiff = $total2 - $total1;
    // Calculate CPU usage percentage
    $cpuUsage = ($totalDiff - $idleDiff) / $totalDiff * 100;

    return $cpuUsage;
}

# device model
$device_model = shell_exec('getprop ro.product.model');
$device_model = trim($device_model);

# os
$android_version = shell_exec('getprop ro.build.version.release');
$android_version = trim($android_version);
$os = "Android $android_version";
$distro = ""; // Customize for your environment if needed

# hostname
$hostname = php_uname('n');

# uptime
$uptime = shell_exec('cat /proc/uptime');
$uptime = explode(' ', $uptime);
$uptime_seconds = intval(trim($uptime[0]));
$uptime_minutes = intval($uptime_seconds / 60 % 60);
$uptime_hours = intval($uptime_seconds / 60 / 60 % 24);
$uptime_days = intval($uptime_seconds / 60 / 60 / 24);
$uptime_sec = $uptime_seconds % 60; // Sisa detik setelah menit

# date
date_default_timezone_set('Asia/Jakarta');
$current_date = date('Y-m-d H:i:s');

# cpu used
$cpu_used = round(getCpuUsage(), 2);

# kernel
$kernel_info = php_uname('r');
?>
<table border="1px">
	<thead>
		<tr colspan="2">
			<th>System</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>Device Model</td>
			<td><?=htmlentities($device_model) ?></td>
		</tr>
		<tr>
			<td>OS</td>
			<td><?=$os ?> <?=$distro ?></td>
		</tr>
		<tr>
			<td>Hostname</td>
			<td><?=$hostname ?></td>
		</tr>
		<tr>
			<td>Uptime</td>
			<td><?=$uptime_days ?> days, <?=$uptime_hours ?> hours, <?=$uptime_minutes ?> minutes, <?=$uptime_sec ?> seconds</td>
		</tr>
		<tr>
			<td>Current Date</td>
			<td><?=$current_date ?></td>
		</tr>
		<tr>
			<td>CPU Usage</td>
			<td><?=$cpu_used ?>% / 100%</td>
		</tr>
		<tr>
			<td>Kernel</td>
			<td><?=$kernel_info ?></td>
		</tr>
	</tbody>
</table>
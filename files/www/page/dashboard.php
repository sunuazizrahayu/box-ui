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

function batStatusCheck() {
	$battery_state = shell_exec('dumpsys battery | grep status | cut -d \':\' -f2');
    switch ($battery_state) {
        case 1:
            return "Unknown";
        case 2:
            return "Charging";
        case 3:
            return "Discharging";
        case 4:
            return "Not charging";
        case 5:
            return "Full";
    }
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

# battery ac
$ac_powered = shell_exec('dumpsys battery | grep AC | cut -d \':\' -f2');
$battery_status = batStatusCheck();
$battery_level = shell_exec('dumpsys battery | grep level | cut -d \':\' -f2');
$battery_current = shell_exec('cat /sys/class/power_supply/battery/current_now');
if (strlen(trim($battery_current)) >= 5) {
    $battery_current = round(shell_exec('cat /sys/class/power_supply/battery/current_now') / 1000);
}
$battery_voltage = round(shell_exec('cat /sys/class/power_supply/battery/voltage_now') / 1000000, 2);
$battery_temperature = shell_exec('dumpsys battery | grep temperature | cut -d \':\' -f2') / 10;
?>
<table border="1px">
	<tbody>
		<tr colspan="2" style="font-weight: bold;">
			<td>System</td>
		</tr>
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
		<tr>
			<td colspan="2"></td>
		</tr>

		<tr colspan="2" style="font-weight: bold;">
			<td>Battery</td>
		</tr>
		<tr>
			<td>Power</td>
			<td><?=strtoupper($ac_powered) ?></td>
		</tr>
		<tr>
			<td>Status</td>
			<td><?=strtoupper($battery_status) ?></td>
		</tr>
		<tr>
			<td>Level</td>
			<td><?=$battery_level ?></td>
		</tr>
		<tr>
			<td>Current</td>
			<td><?=$battery_current ?> mA</td>
		</tr>
		<tr>
			<td>Voltage</td>
			<td><?=$battery_voltage ?> V</td>
		</tr>
		<tr>
			<td>Temperature</td>
			<td><?=$battery_temperature ?> °C</td>
		</tr>
	</tbody>
</table>
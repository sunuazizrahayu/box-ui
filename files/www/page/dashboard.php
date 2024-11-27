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

<?php
$cpu_info = shell_exec('cat /proc/cpuinfo | grep -i "^model name" | awk -F": " \'{print $2}\' | head -1 | sed \'s/ \+/ /g\'');

$cpu_freq = shell_exec('cat /proc/cpuinfo | grep -i "^cpu MHz" | awk -F": " \'{print $2}\' | head -1');
$cpu_freq = intval(trim($cpu_freq));
if (empty($cpu_freq)) {
    $cpu_freq = shell_exec('cat /sys/devices/system/cpu/cpu0/cpufreq/cpuinfo_max_freq');
    $cpu_freq = intval(trim($cpu_freq)) / 1000;
}
$cpu_bogomips = shell_exec('cat /proc/cpuinfo | grep -i "^bogomips" | awk -F": " \'{print $2}\' | head -1');

# cpu load average
$cpu_nb = shell_exec('cat /proc/cpuinfo | grep "^processor" | wc -l');
$cpu_nb = intval(trim($cpu_nb));

$loadavg = shell_exec('cat /proc/loadavg');
$loadavg_arr = explode(' ', $loadavg);

$load_1 = floatval($loadavg_arr[0]);
$load_2 = floatval($loadavg_arr[1]);
$load_3 = floatval($loadavg_arr[2]);

$load_1_percent = round(($load_1 / $cpu_nb) * 100);
$load_2_percent = round(($load_2 / $cpu_nb) * 100);
$load_3_percent = round(($load_3 / $cpu_nb) * 100);
?>
		<tr colspan="2" style="font-weight: bold;">
			<td>CPU</td>
		</tr>
		<tr>
			<td>CPU Model</td>
			<td><?=$cpu_info ?></td>
		</tr>
		<tr>
			<td>CPU Frequency</td>
			<td><?=$cpu_freq ?> MHz</td>
		</tr>
		<tr>
			<td>CPU Bogomips</td>
			<td><?=$cpu_bogomips ?></td>
		</tr>
		<tr>
			<td>Load Average (1 min)</td>
			<td><?=$load_1_percent ?>% (<?=$load_1 ?>)</td>
		</tr>
		<tr>
			<td>Load Average (5 min)</td>
			<td><?=$load_2_percent ?>% (<?=$load_2 ?>)</td>
		</tr>
		<tr>
			<td>Load Average (15 min)</td>
			<td><?=$load_3_percent ?>% (<?=$load_3 ?>)</td>
		</tr>

<?php
$swap_total_kb = shell_exec('cat /proc/meminfo | grep SwapTotal | awk \'{print $2}\'');
$swap_total_gb = intval(trim($swap_total_kb)) / 1024 / 1024; // Convert to GB
$swap_total_gb_rounded = number_format($swap_total_gb, 2);
$swap_total = 'Not Available';
if ($swap_total_kb > 0) {
	$swap_total = $swap_total_gb_rounded .' GB';
}

$swap_free_kb = shell_exec('cat /proc/meminfo | grep SwapFree | awk \'{print $2}\'');
$swap_free_gb = intval(trim($swap_free_kb)) / 1024 / 1024; // Convert to GB
$swap_free_gb_rounded = number_format($swap_free_gb, 2);

$swap_used = 0;
$swap_used_percent = 0;
if ($swap_total_kb > 0) {
	$swap_used_gb = $swap_total_gb_rounded - $swap_free_gb_rounded;
	$swap_used = $swap_used_gb . ' GB';
	$swap_used_percent = number_format((($swap_used_gb / $swap_total_gb_rounded) * 100), 2);
}
?>
		<tr colspan="2" style="font-weight: bold;">
			<td>Disk Info</td>
		</tr>
		<tr>
			<td>Total Swap</td>
			<td><?=$swap_total ?></td>
		</tr>
		<tr>
			<td>Used Swap</td>
			<td><?=$swap_used ?></td>
		</tr>


<?php
$total_memory_kb = shell_exec('cat /proc/meminfo | grep MemTotal | awk \'{print $2}\'');
$total_memory_gb = intval(trim($total_memory_kb)) / 1024 / 1024; // Convert to GB
$total_memory_gb_rounded = number_format($total_memory_gb, 2);
$total_memory_mb_rounded = number_format($total_memory_gb * 1024, 2);

$free_memory_kb = shell_exec('cat /proc/meminfo | grep MemFree | awk \'{print $2}\'');
$free_memory_gb = intval(trim($free_memory_kb)) / 1024 / 1024; // Convert to GB
$free_memory_gb_rounded = number_format($free_memory_gb, 2);
$free_memory_mb_rounded = number_format($free_memory_gb * 1024, 2);

$buffers_memory_kb = shell_exec('cat /proc/meminfo | grep Buffers | awk \'{print $2}\'');
$buffers_memory_gb = intval(trim($buffers_memory_kb)) / 1024 / 1024; // Convert to GB
$buffers_memory_gb_rounded = number_format($buffers_memory_gb, 2);
$buffers_memory_mb_rounded = number_format($buffers_memory_gb * 1024, 2);

$cached_memory_kb = shell_exec('cat /proc/meminfo | grep ^Cached | awk \'{print $2}\'');
$cached_memory_gb = intval(trim($cached_memory_kb)) / 1024 / 1024; // Convert to GB
$cached_memory_gb_rounded = number_format($cached_memory_gb, 2);
$cached_memory_mb_rounded = number_format($cached_memory_gb * 1024, 2);

$used_memory_gb = $total_memory_gb_rounded - $free_memory_gb_rounded - $buffers_memory_gb_rounded - $cached_memory_gb_rounded;
$used_memory_mb = $total_memory_mb_rounded - $free_memory_mb_rounded - $buffers_memory_mb_rounded - $cached_memory_mb_rounded;
$used_memory_percent = number_format((($used_memory_gb / $total_memory_gb_rounded) * 100),2);

$available_memory_gb = $free_memory_gb_rounded + $buffers_memory_gb_rounded + $cached_memory_gb_rounded;
$available_memory_mb = $free_memory_mb_rounded + $buffers_memory_mb_rounded + $cached_memory_mb_rounded;


$total_memory_text = $total_memory_mb_rounded.' MB';
if ($total_memory_gb_rounded >= 1) {
	$total_memory_text = $total_memory_gb_rounded.' GB';
}
$total_used_memory_text = $used_memory_mb.' MB';
if ($used_memory_gb >= 1) {
	$total_used_memory_text = $used_memory_gb.' GB';
}
$available_memory_text = $available_memory_mb.' MB';
if ($available_memory_gb >= 1) {
	$available_memory_text = $available_memory_gb.' GB';
}
?>
		<tr>
			<td>RAM Total</td>
			<td><?=$total_memory_text ?></td>
		</tr>
		<tr>
			<td>RAM Used</td>
			<td><?=$total_used_memory_text ?></td>
		</tr>
		<tr>
			<td>RAM Free</td>
			<td><?=$available_memory_text ?></td>
		</tr>
	</tbody>
</table>
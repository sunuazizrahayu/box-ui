<?php
function getDeviceModel()
{
	$device_model = shell_exec('getprop ro.product.model');
	$device_model = trim($device_model);
	return $device_model;
}
function getKernelVersion()
{
	return php_uname('r');
}
function getUptime($format = '')
{
	$uptime = shell_exec('cat /proc/uptime');
	$uptime = explode(' ', $uptime);
	$uptime_seconds = intval(trim($uptime[0]));

	// Menghitung waktu dalam detik
	$uptime_days = intval($uptime_seconds / 60 / 60 / 24);
	$uptime_hours = intval($uptime_seconds / 60 / 60 % 24);
	$uptime_minutes = intval($uptime_seconds / 60 % 60);
	$uptime_sec = $uptime_seconds % 60;

	// Ganti format dengan nilai yang sesuai
	$formatted_uptime = str_replace(
		['%d', '%h', '%m', '%s'],
		[
			$uptime_days,       // Ganti %d dengan jumlah hari
			$uptime_hours,      // Ganti %h dengan jumlah jam
			$uptime_minutes,    // Ganti %m dengan jumlah menit
			$uptime_sec         // Ganti %s dengan jumlah detik
		],
		$format
	);

	return $formatted_uptime;
}
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
function getTime($format='')
{
	date_default_timezone_set('Asia/Jakarta');
	$current_date = date($format);
	return $current_date;
}

$data['device_model'] = getDeviceModel();
$data['kernel_version'] = getKernelVersion();
$data['uptime'] = getUptime('%dd %hh %mm %ss');
$data['cpu_usage'] = getCpuUsage();
$data['time_now'] = getTime('Y-m-d H:i:s');
$data['page_title'] = 'Dashboard';
view('page/dashboard', $data);
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
function getMemory()
{
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
	$buffers_text = $buffers_memory_mb_rounded . ' MB';
	if ($buffers_memory_gb >= 1) {
		$buffers_text = $buffers_memory_gb_rounded . ' GB';
	}

	$cached_memory_kb = shell_exec('cat /proc/meminfo | grep ^Cached | awk \'{print $2}\'');
	$cached_memory_gb = intval(trim($cached_memory_kb)) / 1024 / 1024; // Convert to GB
	$cached_memory_gb_rounded = number_format($cached_memory_gb, 2);
	$cached_memory_mb_rounded = number_format($cached_memory_gb * 1024, 2);
	$cached_text = $cached_memory_mb_rounded . ' MB';
	if ($cached_memory_gb >= 1) {
		$cached_text = $cached_memory_gb_rounded . ' GB';
	}

	$used_memory_gb = floatval($total_memory_gb_rounded) - floatval($free_memory_gb_rounded) - floatval($buffers_memory_gb_rounded) - floatval($cached_memory_gb_rounded);
	$used_memory_mb = floatval($total_memory_mb_rounded) - floatval($free_memory_mb_rounded) - floatval($buffers_memory_mb_rounded) - floatval($cached_memory_mb_rounded);
	$used_memory_percent = number_format((($used_memory_gb / $total_memory_gb_rounded) * 100),2);

	$available_memory_gb = floatval($free_memory_gb_rounded) + floatval($buffers_memory_gb_rounded) + floatval($cached_memory_gb_rounded);
	$available_memory_mb = floatval($free_memory_mb_rounded) + floatval($buffers_memory_mb_rounded) + floatval($cached_memory_mb_rounded);


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

	return [
		'total_memory_text' => $total_memory_text,
		'total_used_memory_text' => $total_used_memory_text,
		'total_available_memory_text' => $available_memory_text,
		'buffered_text' => $buffers_text,
		'cached_text' => $cached_text,
		'free_text' => $available_memory_text,
	];
}

$data['device_model'] = getDeviceModel();
$data['kernel_version'] = getKernelVersion();
$data['uptime'] = getUptime('%dd %hh %mm %ss');
$data['cpu_usage'] = getCpuUsage();
$data['time_now'] = getTime('Y-m-d H:i:s');
$data['memory'] = getMemory();
$data['page_title'] = 'Dashboard';
view('page/dashboard', $data);
<?php
if (!LOGGED_IN) {
	redirect('auth/login.php');
} else {
	// include 'page/sysinfo.php';
	redirect('dashboard');
}
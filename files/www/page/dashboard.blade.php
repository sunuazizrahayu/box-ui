@extends('layout/app')
@section('page_title', 'Dashboard')
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
	<h1>Overview</h1>
</section>

<!-- Main content -->
<section class="content">

	<!-- System -->
	<div class="box">
		<div class="box-header with-border">
			<h3 class="box-title">System</h3>
		</div>
		<div class="box-body no-padding">
			<table class="table">
				<tr>
					<td>Model</td>
					<td>{{ $device_model }}</td>
				</tr>
				<tr>
					<td>Firmware Version</td>
					<td></td>
				</tr>
				<tr>
					<td>Kernel Version</td>
					<td>{{ $kernel_version }}</td>
				</tr>
				<tr>
					<td>Local Time</td>
					<td>{{ $time_now }}</td>
				</tr>
				<tr>
					<td>Uptime</td>
					<td><?=$uptime ?></td>
				</tr>
				<tr>
					<td>Temperature</td>
					<td>-</td>
				</tr>
				<tr>
					<td>CPU Usage</td>
					<td>{{ number_format($cpu_usage, 2) }}%</td>
				</tr>
			</table>
		</div>
		<!-- /.box-body -->
	</div>
	<!-- /.box -->

	<!-- Memory / RAM -->
	<div class="box">
		<div class="box-header with-border">
			<h3 class="box-title">Memory</h3>
		</div>
		<div class="box-body no-padding">
			<table class="table">
				<tr>
					<td>RAM Total</td>
					<td></td>
				</tr>
				<tr>
					<td>Used</td>
					<td></td>
				</tr>
				<tr>
					<td>Buffered</td>
					<td></td>
				</tr>
				<tr>
					<td>Cached</td>
					<td></td>
				</tr>
				<tr>
					<td>Uptime</td>
					<td></td>
				</tr>
				<tr>
					<td>Temperature</td>
					<td></td>
				</tr>
				<tr>
					<td>CPU Usage</td>
					<td></td>
				</tr>
			</table>
		</div>
		<!-- /.box-body -->
	</div>
	<!-- /.box -->

	<!-- Storage -->
	<div class="box">
		<div class="box-header with-border">
			<h3 class="box-title">Storage</h3>
		</div>
		<div class="box-body no-padding">
			<table class="table">
				<tr>
					<td>Disk space</td>
					<td></td>
				</tr>
				<tr>
					<td>Temp space</td>
					<td></td>
				</tr>
				<tr>
					<td>/dev/root</td>
					<td></td>
				</tr>
			</table>
		</div>
		<!-- /.box-body -->
	</div>
	<!-- /.box -->

	<!-- Storage -->
	<div class="box">
		<div class="box-header with-border">
			<h3 class="box-title">Port status</h3>
		</div>
		<div class="box-body no-padding">
			<table class="table">
				<tr>
					<td>XXXX</td>
					<td></td>
				</tr>
			</table>
		</div>
		<!-- /.box-body -->
	</div>
	<!-- /.box -->

	<!-- Network -->
	<div class="box">
		<div class="box-header with-border">
			<h3 class="box-title">Network</h3>
		</div>
		<div class="box-body no-padding">
			<table class="table">
				<tr>
					<td>XXXX</td>
					<td></td>
				</tr>
			</table>
		</div>
		<!-- /.box-body -->
	</div>
	<!-- /.box -->

	<!-- Active DHCP Leases -->
	<div class="box">
		<div class="box-header with-border">
			<h3 class="box-title">Active DHCP Leases</h3>
		</div>
		<div class="box-body no-padding">
			<table class="table">
				<tr>
					<td>Disk space</td>
					<td></td>
				</tr>
				<tr>
					<td>Temp space</td>
					<td></td>
				</tr>
				<tr>
					<td>/dev/root</td>
					<td></td>
				</tr>
			</table>
		</div>
		<!-- /.box-body -->
	</div>
	<!-- /.box -->

</section>
<!-- /.content -->
@endsection
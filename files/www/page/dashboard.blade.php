@extends('layout/app')
@section('page_title', 'Dashboard')
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
	<h1>Overview</h1>
</section>

<!-- Main content -->
<section class="content">

	<div class="row">
		<div class="col-md-12">
			<div class="nav-tabs-custom">
				<ul class="nav nav-tabs">
					<li class="active"><a href="#tab_1" data-toggle="tab">System</a></li>
					<li><a href="#tab_2" data-toggle="tab">Memory</a></li>
					<li><a href="#tab_3" data-toggle="tab">Storage</a></li>
					<li><a href="#tab_4" data-toggle="tab">Port Status</a></li>
					<li><a href="#tab_5" data-toggle="tab">Network</a></li>
					<li><a href="#tab_6" data-toggle="tab">Active DHCP Leases</a></li>
				</ul>
				<div class="tab-content no-padding">
					<div class="tab-pane active" id="tab_1">
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
					<div class="tab-pane" id="tab_2">
						<table class="table">
							<tr>
								<td>RAM Total</td>
								<td>{{ $memory['total_memory_text'] }}</td>
							</tr>
							<tr>
								<td>Used</td>
								<td>{{ $memory['total_used_memory_text'] }}</td>
							</tr>
							<tr>
								<td>Buffered</td>
								<td>{{ $memory['buffered_text'] }}</td>
							</tr>
							<tr>
								<td>Cached</td>
								<td>{{ $memory['cached_text'] }}</td>
							</tr>
							<tr>
								<td>Free</td>
								<td>{{ $memory['free_text'] }}</td>
							</tr>
						</table>
					</div>
					<div class="tab-pane" id="tab_3">
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
					<div class="tab-pane" id="tab_4">
						<table class="table">
							<tr>
								<td>XXXX</td>
								<td></td>
							</tr>
						</table>
					</div>
					<div class="tab-pane" id="tab_5">
						<table class="table">
							<tr>
								<td>XXXX</td>
								<td></td>
							</tr>
						</table>
					</div>
					<div class="tab-pane" id="tab_6">
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
				</div>
			</div>
		</div>
	</div>

</section>
<!-- /.content -->
@endsection
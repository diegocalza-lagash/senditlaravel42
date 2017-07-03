@extends("layouts.master")
@section('title','Todos Los Trabajos')
@section('content')
@section('trabajos')
@parent
<li><a href="/dataform">Informe Seguimiento</a></li>
@stop
@if(Session::has('mensaje_error'))
<div style="margin-left: 11%;" class="alert alert-danger">{{ Session::get('mensaje_error') }}</div>
@endif
<h1 class="sub_header">Informes Técnicos</h1>
<div class="dataTable_wrapper">
	<div class="data_table" style="width: 100%;">
		<table style="width: 60%;" id= "Table_RTech" class="table table-striped table-hover table-bordered table-condensed listar-act">
			<thead>
				<tr>
					<th>Exportar Excel</th>
					<th>Trabajo</th>
					<th>Sub-Trabajo</th>
					<th>Fecha de Envío</th>
					<th>Enviado por</th>
					<th>Orden de Mantención</th>
					<!--<th>Descripción de la OM</th>
					<th>Tipo de Equipo</th>
					<th>Descripción del Equipo</th>
					<th>Código</th>
					<th>Fecha</th>
					<th>Informe Realizado por</th>
					<th>Empresa Ejecutora</th>
					<th>Supervisor Planta</th>
					<th>Ubicación Técnica/TAG</th>-->

				</tr>
			</thead>
			<tbody>
				<?php
					$id = 1;
				?>
				@foreach ($docRTech as $row)

					<tr>
						<td  class="button_excel"><a href="report_tech/excel/{{$row['Entry']['Id']}}" target="_blank" ><button class="btn btn-block btn-success btn-xs">Excel</button></a></td>
						<td><?php echo $row['Entry']['Trabajo'];?></td>
						<td><?php echo $row['Entry']['SubTrabajo'];?></td>
						<td><?php
						$startTime = new DateTime($row['Entry']['StartTime']);
						$startTime->setTimezone(new DateTimeZone('America/Santiago'));
						echo $startTime->format('j F, Y, H:i a');
							?>
							<!--<div>
							<?php $uploaded= new DateTime($row['Entry']['CompleteTime']) ?>
								<span><b>Subido: </b>{{ $uploaded->format('d-F-Y H:i a') }}</span>
							</div>-->
						</td>
						<td><?php echo $row['Entry']['UserFirstName']." ".$row['Entry']['UserLastName'];?></td>
						<td><?php echo $row['AFAL']['Order_manag'];?></td>



					</tr>
					<?php $id++; ?>
				@endforeach

			</tbody>
		</table>
</div>


</div>
@stop
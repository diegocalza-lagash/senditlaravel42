@extends("layouts.master")
@section('title','Lista De Trabajos')
@section('sidebar')
@parent

@stop
@section('trabajos')
@parent
<li><a href="/dataform">Inicio</a></li>
<li><a href="/download/pdf/" target="_blank">Exportar a PDF</a></li>
<li><a href="/download/excel" target="_blank">Exportar a Excel</a></li>
@stop
@section('content')
<?php $docRepor = iterator_to_array($docRepor);?>
<h1 class="sub_header">Trabajos Subidos {{count($docRepor)}}</h1>
	<div class="dataTable_wrapper">

		<div class="dataTable_header">
			<div class = "dataTable_download">
			<!--<a href="/download">Exportar</a>-->
			</div>
			<div class="dataTable_filtro"></div>
		</div>
		<div class="data_table">
			<table class="table table-striped table-condensed table-hover table-bordered">
				<thead>
					<tr>
						<th>Fecha de Envío</th>
						<th>Enviado por</th>
						<th>Equipo</th>
						<th>Identificación Equipo</th>
						<th>Ubicación</th>
						<th>Sistema de bloqueo</th>
						<th>Trabajo</th>
						<th>SubTrabajo</th>
						<th>Fecha de Inicio Programada</th>
						<th>Fecha de Término Programada</th>
						<th>Fecha de Inicio Real</th>
						<th>Fecha de Término Real</th>
						<th>Avance</th>
						<th>Observaciones</th>
						<th>Foto 1</th>
					</tr>
				</thead>
				<tbody>
					@foreach ($docRepor as $row)
						<tr>

							<td><?php

							$startTime = new DateTime($row['Entry']['StartTime']);
							//$startTime->setTimezone(new DateTimeZone('America/Santiago'));
							echo $startTime->format('j F, Y, g:i a');
								?>
								<!--<div>
								<?php $uploaded= new DateTime($row['Entry']['CompleteTime']) ?>
									<span><b>Subido: </b>{{ $uploaded->format('d-F-Y g:i a') }}</span>
								</div>-->
							</td>
							<td><?php echo $row['Entry']['UserFirstName']." ".$row['Entry']['UserLastName'];?></td>
							<td><?php echo $row['EQUIPMENT']['EQUIPMENT_NAME'];?></td>
							<td><?php echo $row['EQUIPMENT']['IDENTIFICATION_EQUIPMENT']['IDENTIFICATION_NAME'];?></td>
							<td><?php echo $row['EQUIPMENT']['LOCALIZATION_EQUIPMENT']['LOCALIZATION_NAME'];?></td>
							<td><?php echo $row['EQUIPMENT']['BLOCK_SYSTEM'];?></td>
							<td><?php echo $row['EQUIPMENT']['WORK']['WORK_NAME'];?></td>
							<td><?php echo $row['EQUIPMENT']['WORK']['SUBWORK']['SUBWORK_NAME'];?></td>
							<td><?php echo $row['EQUIPMENT']['DATE_START_PROGRAMMED'];?></td>
							<td><?php echo $row['EQUIPMENT']['DATE_END_PROGRAMMED'];?></td>
							<td><?php
							$DATE_START_REAL = new DateTime($row['EQUIPMENT']['WORK']['SUBWORK']['DATE_START_REAL']);
							$DATE_START_REAL->setTimezone(new DateTimeZone('America/Santiago'));
							echo $DATE_START_REAL->format('d-m-Y, g:i a');
							?></td>
							<td><?php
							$DATE_END_REAL = new DateTime($row['EQUIPMENT']['WORK']['SUBWORK']['DATE_END_REAL']);
							$DATE_END_REAL->setTimezone(new DateTimeZone('America/Santiago'));
							echo $DATE_END_REAL->format('d-m-Y, g:i a');
							?></td>
							<td><?php echo $row['EQUIPMENT']['WORK']['SUBWORK']['POOP']."%"?></td>
							<td><?php echo $row['EQUIPMENT']['WORK']['SUBWORK']['OBSERVATIONS'];?></td>
							<td>
								<?php
								if ($row['EQUIPMENT']['WORK']['PHOTOS']['PHOTO1']!= "") {

										echo '<a target="_blank" href="'.$row['EQUIPMENT']['WORK']['PHOTOS']['PHOTO1'].'">Ver Foto</a>';
								}else{echo "-";}
								?>
							</td>


						</tr>
					@endforeach
				</tbody>
			</table>
		</div>

	</div>
@stop


@extends("layouts.master")
@section('title','Todos Los Trabajos')
@section('content')
<h1 class="sub_header">Informes Técnicos</h1>
<div class="dataTable_wrapper">
	<div class="data_table">
		<table id= "lista-crud" class="table table-striped table-hover table-bordered table-condensed listar-act">
			<thead>
				<tr>

					<th>Fecha de Envío</th>
					<th>Enviado por</th>
					<th>Orden de Mantención</th>
					<th>Ver</th>
					<th>Descargar</th>
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
						<td><?php
						$startTime = new DateTime($row['Entry']['StartTime']);
						$startTime->setTimezone(new DateTimeZone('America/Santiago'));
						echo $startTime->format('j F, Y, g:i a');
							?>
							<!--<div>
							<?php $uploaded= new DateTime($row['Entry']['CompleteTime']) ?>
								<span><b>Subido: </b>{{ $uploaded->format('d-F-Y g:i a') }}</span>
							</div>-->
						</td>
						<td><?php echo $row['Entry']['UserFirstName']." ".$row['Entry']['UserLastName'];?></td>
						<td><?php echo $row['Entry']['AnswersJson']['report_technical']['order_manag'];?></td>
						<td><a href="report_tech/pdf/1/{{$row['Entry']['Id']}}" target="_blank" ><button class="btn btn-block btn-primary btn-xs">Ver</button></a></td>
						<td><a href="report_tech/pdf/2/{{$row['Entry']['Id']}}" target="_blank" ><button class="btn btn-block btn-success btn-xs">Descargar</button></a></td>

						<!--<td><?php echo $row['Entry']['AnswersJson']['report_technical']['mode_fail'];?>
						</td>
						<td><?php echo $row['Entry']['AnswersJson']['report_technical']['equipment'];?></td>
						<td><?php echo $row['Entry']['AnswersJson']['report_technical']['equipment_desc'];?>
						</td>
						<td><?php echo $row['Entry']['AnswersJson']['report_technical']['code'];?>
						</td>
						<td><?php echo $row['Entry']['AnswersJson']['report_technical']['date_report_tech'];?>
						</td>
						<td><?php echo $row['Entry']['AnswersJson']['report_technical']['report_by'];?>
						</td>
						<td><?php echo $row['Entry']['AnswersJson']['report_technical']['company_exec'];?>
						</td>
						<td><?php echo $row['Entry']['AnswersJson']['report_technical']['supervisor_plant'];?>
						</td>
						<td><?php echo $row['Entry']['AnswersJson']['report_technical']['Loc_technical'];?>
						</td>-->

					</tr>
					<?php $id++; ?>
				@endforeach

			</tbody>
		</table>
</div>


</div>
@stop
<head>
	<link rel="stylesheet" href="{{ URL::asset('assets/css/css_rtech.css') }}">
	<link rel="stylesheet" href="{{ URL::asset('assets/css/bootstrap.min.css') }}">
	<!--<link rel="stylesheet" href="{{ URL::asset('assets/css/reset.css') }}">-->
	<style>
		table{
			border-spacing:0;

			/*background-color: transparent;*/
		}


	</style>
</head>
<div class="Report_Tech">
	<div class="logos">
	<ul>
		<li><div class="client"><img src="{{asset('photos/logos/arauco.jpg')}}"></div></li>
		<li><div class="titulo"><h1>INFORME TECNICO</h1></div></li>
		<li><div class="empresa"><img src="{{asset('photos/logos/rudel.jpg')}}"></div></li>
		<!--<li class="client"><img src="{{asset('photos/logos/arauco.jpg')}}"></li>
		<li><span>INFORME TECNICO</span></li>
		<li class="empresa"><img src="{{asset('photos/logos/rudel.jpg')}}"></li>-->

	</ul>

	</div>
	<div class="nav">
		<table class="table table-striped table-hover table-bordered table-condensed listar-act">
			<thead>
				<tr>
					<th style="width: 26%;">Orden de Mantenimiento</th>
					<th style="width: 46%;">Descripción de la OM (Modo de Falla)</th>
					<th>Código</th>
				</tr>
				<tr></tr>

			</thead>
			<tbody>
				<tr>
					<td>{{$rt['Entry']['AnswersJson']['report_technical']['order_manag']}}</td>
					<td>{{$rt['Entry']['AnswersJson']['report_technical']['mode_fail']}}</td>
					<td>{{$rt['Entry']['AnswersJson']['report_technical']['code']}}</td>
				</tr>
				<tr>

					<th>Tipo de Equipo</th>
					<th>Descripción del equipo</th>
					<th>Fecha</th>

				</tr>
				<tr>
					<td>{{$rt['Entry']['AnswersJson']['report_technical']['equipment']}}</td>
					<td>{{$rt['Entry']['AnswersJson']['report_technical']['equipment_desc']}}</td>
					<td>{{$rt['Entry']['AnswersJson']['report_technical']['date_report_tech']}}</td>
				</tr>

			</tbody>
		</table>
		<table class="table table-striped table-bordered table-condensed listar-act">
			<thead>

				<tr>

					<th>Informe realizado por</th>
					<th>Empresa Ejecutora</th>
					<th>Supervisor Planta</th>
					<th>Ubicación Técnica/TAG</th>


				</tr>
			<tbody>
				<tr>
					<td>{{$rt['Entry']['AnswersJson']['report_technical']['report_by']}}</td>
					<td>{{$rt['Entry']['AnswersJson']['report_technical']['company_exec']}}</td>
					<td>{{$rt['Entry']['AnswersJson']['report_technical']['supervisor_plant']}}</td>
					<td>{{$rt['Entry']['AnswersJson']['report_technical']['Loc_technical']}}</td>
				</tr>
			</tbody>

			</thead>
		</table>




		<!--<div>Orden de Mantenimiento</div>
		<div>Descripción de la OM (Modo de Falla)</div>
		<div>Código</div>


		<div class="OM">{{$rt['Entry']['AnswersJson']['report_technical']['order_manag']}}</div>
		<div class="mode_fail">{{$rt['Entry']['AnswersJson']['report_technical']['mode_fail']}}</div>

		<div class="equipment">{{$rt['Entry']['AnswersJson']['report_technical']['equipment']}}</div>-->
	</div>

	<div class="status_i">
		<div class="how_find_out">

		</div>
		<div class="images">
			<?php
			$l1 = generateLinkPhotos($rt['Entry']['Id'],$rt['ProviderId'],$rt['Entry']['AnswersJson']['state_i']['photo1_i']);
			$l2 = generateLinkPhotos($rt['Entry']['Id'],$rt['ProviderId'],$rt['Entry']['AnswersJson']['state_i']['photo2_i']);
			?>
			<div class="photo1">
				<img src="{{ $l1 }}">
				<div class="leyend"><span></span></div>
			</div>
			<div class="photo2">
				<img src="{{ $l2 }}">
				<div class="leyend"><span></span></div>
			</div>
		</div>
		<div class="medition"></div>

	</div>
	<div class="status_f">
		<div class="how_leaved">

		</div>
		<div class="images">
			<?php
			$l1 = generateLinkPhotos($rt['Entry']['Id'],$rt['ProviderId'],$rt['Entry']['AnswersJson']['equiment_state_final']['photo1_f']);
			$l2 = generateLinkPhotos($rt['Entry']['Id'],$rt['ProviderId'],$rt['Entry']['AnswersJson']['equiment_state_final']['photo2_f']);
			?>
			<div class="photo1">
				<img src="{{ $l1 }}">
				<div class="leyend"><span></span></div>
			</div>
			<div class="photo2">
				<img src="{{ $l2 }}">
				<div class="leyend"><span></span></div>
			</div>
		</div>
		<div class="medition"></div>

	</div>

	</div>

</div>
<?php
session_start();

if (isset($_SESSION["ETH"])) {

	include_once 'header.php';
?>

	<title> Profilo DApp </title>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/web3@latest/dist/web3.min.js"></script>

	<script type='text/javascript'>
		$(document).ready(function() {
			$('.cartNumber').replaceWith(JSON.parse(localStorage.getItem("cart") || "[]").length);
		});
	</script>

	<!--https://github.com/ChainSafe/web3.js-->


	<script type="module" src="includes/JavaScript/profileETH.js"></script>

	<p id="appendAlert"></p>

	<div class="container" style="margin-top: 55px;">

		<ul class="nav nav-tabs nav-fill">

			<li class="nav-item">
				<a class="nav-link" aria-current="page" href="#" id="viewBalanceBtn"><span>Visualizza bilancio</span></a>
			</li>

			<li class="nav-item"></li>

			<li class="nav-item"></li>

		</ul>

		<div style="padding-left: 110px;"><span id="balanceAfter" style="display:none;"></span></div>

	</div>

	<br>

	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.23/css/dataTables.bootstrap4.min.css">
	<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js">
	</script>
	<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.23/js/dataTables.bootstrap4.min.js">
	</script>

	<div class="container">

		<div id='rowTable' class="row">

			<div class="table-responsive">

				<table class="table" id="tableShowETH" style="margin-top: 20px;">

					<thead class="table-dark">

						<tr>
							<th scope="col"> #Numero acquisto </th>
							<th scope="col"> Contenuto </th>
							<th scope="col"> Data acquisto </th>
							<th scope="col"> Ether </th>
							<th scope="col"> Stato </th>
						</tr>

					</thead>

					<tbody id="tr_dynamic"></tbody>

				</table>

			</div>

		</div>

	</div>

	<br> <br>

<?php

	include_once 'footer.php';
}
?>
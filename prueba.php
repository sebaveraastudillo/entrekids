<?php

include("repository.php");

function renderHtml() {

	//$response = file_get_contents("https://entrekidscl.s3.amazonaws.com/DummyData.json");
	//$data = json_decode($response);


	

	try {

		$repo = new Repo();
		$dataList = $repo->getDataList();
		$dataDetail = $repo->getDataSellers();

		$content = '<!DOCTYPE html>
		<html>
		<head>
			<meta charset="utf-8">
			<meta name="viewport" content="width=device-width, initial-scale=1">
			<title></title>
		</head>
		<body>
			<table>
				<thead>
					<tr>
						<td>Proveedor</td>
						<td>Mes</td>
						<td>Monto Vendido</td>
						<td>Categoria</td>
					</tr>
				</thead>
				<tbody>';

		if (!file_exists("proveedor")) {
			mkdir("proveedor", 0777);	
		}
				
		foreach ($dataList as $value) {

			$content .= '<tr>
				<td>
					<a href="proveedor/' . $value["seller_id"] . '">' . $value["seller_name"] . '</a>
				</td>
				<td>
					' . $value["month"] . '
				</td>
				<td>
					' . $value["total_sold"] . '
				</td>
				<td>
					' . $value["tipo"] . '
				</td>
			</tr>';

			$contentDetail = '<!DOCTYPE html>
				<html>
				<head>
					<meta charset="utf-8">
					<meta name="viewport" content="width=device-width, initial-scale=1">
					<title></title>
				</head>
				<body>
					<h1>' . $value["seller_name"] . '</h1>
					<table>
						<thead>
							<tr>
								<td>Mes</td>
								<td>Activo mas vendido en cantidad</td>
								<td>Activo que generó mas dinero</td>
								<td>Activo mas cancelado</td>
							</tr>
						</thead>
						<tbody>';



			if (!empty($dataDetail[$value["seller_id"]])) {

				foreach ($dataDetail[$value["seller_id"]] as $month => $data) {

					$vendido = !empty($data["vendido"]) ? $data["vendido"] : "S/I";
					$dinero = !empty($data["dinero"]) ? $data["dinero"] : "S/I";
					$cancelado = !empty($data["cancelado"]) ? $data["cancelado"] : "S/I";

					$contentDetail .= '<tr>
							<td>
								' . $month . '
							</td>
							<td>
								' . $vendido . '
							</td>
							<td>
								' . $dinero . '
							</td>
							<td>
								' . $cancelado . '
							</td>
						</tr>';	
				}

				$contentDetail .= '</tbody></table></body></html>';

			} else {
				$contentDetail .= '</tbody></table><h3>Sin información</h3></body></html>';
			}

			$handleDetail = fopen("proveedor/" . $value["seller_id"] . ".html", "w+");
			fwrite($handleDetail, $contentDetail);
			fclose($handleDetail);

        }

		$content .= '</tbody>
		</table>
		</body>
		</html>';

		$handle = fopen("index.html", "w+");
		fwrite($handle, $content);
		fclose($handle);

		$repo->closeConn();

	} catch (Exception $e) {
    	echo 'Error Processing Request: ',  $e->getMessage(), "\n";
	}


	



	/**/


}


renderHtml();

?>
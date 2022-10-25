<?php

include("db_connection.php");

class Repo {

	private $conn;

	public function __construct() {
		$this->conn = openCon();
	}

	public function getDataList() {

		$query = 'SELECT 
					SUM(total) AS monto_mes, 
					MONTH(t.created) AS mes, 
					p.nombre, 
					p.id, 
					IF(p2.id IS NOT NULL, "producto", IF(e.id IS NOT NULL, "actividad", "otro")) AS tipo 
				FROM transaccion t 
				JOIN item i ON t.id = i.trasaccion_id 
				JOIN actividad_evento ae ON i.evento_id = ae.id 
				JOIN actividad a ON ae.actividad_id = a.id 
				JOIN proveedor p ON a.proveedor_id = p.id 
				LEFT JOIN paquete p2 ON i.id = p2.item_id 
				LEFT JOIN entrada e ON i.id = e.item_id 
				WHERE t.estado = "Pagada" 
				GROUP BY mes, p.nombre, tipo';

		$result = $this->conn->query($query);

		$list = [];
		while ($row = $result->fetch_row()) {
			$element["total_sold"] = $row[0];
			$element["seller_id"] = $row[3];
			$element["seller_name"] = $row[2];
			$element["tipo"] = $row[4];
			$element["month"] = $row[1];
			$list[] = $element;
		}

		return $list;

		
	}


	public function getDataSellers() {

		$queryQuantity = 'SELECT c1.cantidad AS mayor_cantidad, c1.mes, c1.nombre AS proveedor, c1.id, c1.activo 
					FROM (
						SELECT 
							SUM(i.cantidad) AS cantidad, 
							MONTH(t.created) AS mes, 
							p.nombre, 
							p.id, 
							a.nombre AS activo 
						FROM transaccion t 
						JOIN item i ON t.id = i.trasaccion_id 
						JOIN actividad_evento ae ON i.evento_id = ae.id 
						JOIN actividad a ON ae.actividad_id = a.id 
						JOIN proveedor p ON a.proveedor_id = p.id 
						WHERE t.estado = "Pagada" 
						GROUP BY mes, p.nombre, activo 
					) c1 
					JOIN (
						SELECT 
							MAX(c.cantidad) AS cantidad_mayor,
							c.nombre 
						FROM (
							SELECT 
								SUM(i.cantidad) AS cantidad, 
								MONTH(t.created) AS mes, 
								p.nombre, 
								a.nombre AS activo 
							FROM transaccion t 
							JOIN item i ON t.id = i.trasaccion_id 
							JOIN actividad_evento ae ON i.evento_id = ae.id 
							JOIN actividad a ON ae.actividad_id = a.id 
							JOIN proveedor p ON a.proveedor_id = p.id 
							WHERE t.estado = "Pagada" 
							GROUP BY mes, p.nombre, activo 
						) c
						GROUP BY c.nombre
					) c2 ON c1.cantidad = c2.cantidad_mayor AND c1.nombre = c2.nombre ';


		$queryMoney = 'SELECT c1.total AS mayor_monto, c1.mes, c1.nombre AS proveedor, c1.id, c1.activo 
						FROM (
							SELECT 
								SUM(t.total) AS total, 
								MONTH(t.created) AS mes, 
								p.nombre, 
								p.id, 
								a.nombre AS activo 
							FROM transaccion t 
							JOIN item i ON t.id = i.trasaccion_id 
							JOIN actividad_evento ae ON i.evento_id = ae.id 
							JOIN actividad a ON ae.actividad_id = a.id 
							JOIN proveedor p ON a.proveedor_id = p.id 
							WHERE t.estado = "Pagada" 
							GROUP BY mes, p.nombre, activo 
						) c1 
						JOIN (
							SELECT 
								MAX(c.total) AS total_maximo,
								c.nombre 
							FROM (
								SELECT 
									SUM(t.total) AS total, 
									MONTH(t.created) AS mes, 
									p.nombre, 
									a.nombre AS activo 
								FROM transaccion t 
								JOIN item i ON t.id = i.trasaccion_id 
								JOIN actividad_evento ae ON i.evento_id = ae.id 
								JOIN actividad a ON ae.actividad_id = a.id 
								JOIN proveedor p ON a.proveedor_id = p.id 
								WHERE t.estado = "Pagada" 
								GROUP BY mes, p.nombre, activo 
							) c
							GROUP BY c.nombre
						) c2 ON c1.total = c2.total_maximo AND c1.nombre = c2.nombre ';

		$queryCancelled = 'SELECT c1.total AS mayor_cantidad, c1.mes, c1.nombre AS proveedor, c1.id,  c1.activo 
					FROM (
						SELECT 
							COUNT(p2.id) AS total, 
							MONTH(t.created) AS mes, 
							p.nombre, 
							p.id, 
							a.nombre AS activo 
						FROM transaccion t 
						JOIN item i ON t.id = i.trasaccion_id 
						JOIN actividad_evento ae ON i.evento_id = ae.id 
						JOIN actividad a ON ae.actividad_id = a.id 
						JOIN proveedor p ON a.proveedor_id = p.id 
						JOIN paquete p2 ON i.id = p2.item_id 
						WHERE t.estado = "Pagada" AND p2.estado = "Cancelado" 
						GROUP BY mes, p.nombre, activo 
					) c1 
					JOIN (
						SELECT 
							MAX(c.cantidad) AS total_maximo,
							c.nombre 
						FROM (
							SELECT 
								COUNT(p2.id) AS cantidad, 
								MONTH(t.created) AS mes, 
								p.nombre, 
								a.nombre AS activo 
							FROM transaccion t 
							JOIN item i ON t.id = i.trasaccion_id 
							JOIN actividad_evento ae ON i.evento_id = ae.id 
							JOIN actividad a ON ae.actividad_id = a.id 
							JOIN proveedor p ON a.proveedor_id = p.id 
							JOIN paquete p2 ON i.id = p2.item_id 
							WHERE t.estado = "Pagada" AND p2.estado = "Cancelado" 
							GROUP BY mes, p.nombre, activo 
						) c
						GROUP BY c.nombre
					) c2 ON c1.total = c2.total_maximo AND c1.nombre = c2.nombre ';

		$resultQ = $this->conn->query($queryQuantity);

		$data = [];
		while ($row = $resultQ->fetch_row()) {
			//$d["seller_name"] = $row[2];
			//$d["activo"] = $row[4];
			//$d["mayor_cantidad"] = $row[0];
			//$d["mes"] = $row[1];
			$data[$row[3]][$row[1]]["vendido"] = $row[4];
		}

		$resultM = $this->conn->query($queryMoney);

		while ($row = $resultM->fetch_row()) {
			//$d["seller_name"] = $row[2];
			//$d["activo"] = $row[4];
			//$d["mayor_monto"] = $row[0];
			//$d["mes"] = $row[1];
			//$data[$row[3]]["mayor_monto"] = $d;
			$data[$row[3]][$row[1]]["dinero"] = $row[4];
		}

		$resultC = $this->conn->query($queryCancelled);

		while ($row = $resultC->fetch_row()) {
			//$d["seller_name"] = $row[2];
			//$d["activo"] = $row[4];
			//$d["cantidad"] = $row[0];
			//$d["mes"] = $row[1];
			//$data[$row[3]]["mas_cancelado"] = $d;
			$data[$row[3]][$row[1]]["cancelado"] = $row[4];
		}

		return $data;
		
	}

	public function closeConn() {
		closeCon($this->conn);
	}

}



?>
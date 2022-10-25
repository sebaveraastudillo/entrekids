
## Respuestas test

### 1.a  Venta mensual por proveedor, separado por producto o actividad

SELECT 
	SUM(total) AS monto_mes, 
	MONTH(t.created) AS mes, 
	p.nombre, 
	IF(p2.id IS NOT NULL, "producto", IF(e.id IS NOT NULL, "actividad", "otro")) AS tipo 
FROM transaccion t 
JOIN item i ON t.id = i.trasaccion_id 
JOIN actividad_evento ae ON i.evento_id = ae.id 
JOIN actividad a ON ae.actividad_id = a.id 
JOIN proveedor p ON a.proveedor_id = p.id 
LEFT JOIN paquete p2 ON i.id = p2.item_id 
LEFT JOIN entrada e ON i.id = e.item_id 
WHERE t.estado = "Pagada" 
GROUP BY mes, p.nombre, tipo;

### 1.b.a. Activo mas vendido segun cantidad por proveedor mensualmente

SELECT c1.cantidad AS mayor_cantidad, c1.mes, c1.nombre AS proveedor, c1.activo 
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
) c2 ON c1.cantidad = c2.cantidad_mayor AND c1.nombre = c2.nombre 


### 1.b.b Activo que más generó dinero por proveedor mensualmente
SELECT c1.total AS mayor_monto, c1.mes, c1.nombre AS proveedor, c1.activo 
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
) c2 ON c1.total = c2.total_maximo AND c1.nombre = c2.nombre 

### 1.c Activos mas cancelados por proveedor mensualmente 

SELECT c1.total AS mayor_cantidad, c1.mes, c1.nombre AS proveedor, c1.activo 
FROM (
	SELECT 
		COUNT(p2.id) AS total, 
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
) c2 ON c1.total = c2.total_maximo AND c1.nombre = c2.nombre 

#### Nota

Para los mas cancelados se tomó el estado "Cancelado" de la tabla "paquete"

### 2 

Se podrian obtener por ejemplo, la cantidad de entradas mensuales que se validan y/o cancelan por proveedor y activo. Nose bien que representa el campo fecha_acceso en la tabla entrada, pero asumiendo que es la fecha que se usa la entrada a una actividad, se podrian obtener los meses que mas se usan los eventos.

### 3 y 4

Primero se debe importar la data de prueba a una BD MySQL, esta se encuentra en el archivo data.sql 

```mysql -h <host> -u <username> -p<password> <database> < data.sql```

Luego se deben configurar las variables para conexion mysql en el archivo db_connection.php

```
$dbhost = "<host>";
$dbuser = "<username>";
$dbpass = "<password>";
$db = "<database>";
```

Para el punto 3 y 4 se debe ejecutar dentro del proyecto el comando 
```php prueba.php```

Se generará el archivo index.html y los html por proveedor para ver su detalle. Para revisar via web, abrir en el navegador

[http://localhost/entrekids](http://localhost/entrekids)


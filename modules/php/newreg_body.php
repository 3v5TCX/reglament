<?php
// 
// Назначение модуля: Обеспечивает создание нового регламента
// Версия: 
// 

	define('is_RUN', true);
	$ROOT_PATH = dirname(__FILE__);
	
	$event ="";
  	if (isset($_GET['event'])) $event = $_GET['event'];


	$parse->set_tpl("rName","");
	$parse->set_tpl("rOrganization","");
	$parse->set_tpl("rRegNumber","");
	$parse->set_tpl("rDescription","");



	$LISTREQ = "";

	$result=pg_query($connection, "SELECT * FROM \"tReqRegulationsList\" WHERE \"rParentId\" = 0");

	$k1=1;
	
	while ($row = pg_fetch_array($result)) // 
  	{ 

		$LISTREQ .= "<p>";
		$LISTREQ .= "<b> $k1. ".$row['rTitle']."</b><br>";

		$LISTREQ .= "<i>".$row['rDescr']."</i><br>
		<textarea style='width:100%; height:50px' name='req".$row['rId']."'></textarea>
		";
		$LISTREQ .= "</p>";
		
		$k2=1;

		$result2 = pg_query($connection, "SELECT * FROM \"tReqRegulationsList\" WHERE \"rParentId\" = ".$row['rId']);
		while ($row2 = pg_fetch_array($result2)) // 
  		{ 

			$LISTREQ .= "<p>";
			$LISTREQ .= "<b> $k1.$k2. ".$row2['rTitle']."</b><br>";

			$LISTREQ .= "<i>".$row2['rDescr']."</i><br>
			<textarea style='width:100%; height:50px' name='req".$row2['rId']."'></textarea>
			";
			$LISTREQ .= "</p>";
			$k2++;
			
  		}
		$k1++;
		$LISTREQ .= "<hr>";
  	}

	$parse->set_tpl("LISTREQ",$LISTREQ);  	

	

	$parse->tpl_parse(); // парсим шаблон

?>
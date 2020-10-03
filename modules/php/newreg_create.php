<?php
// 
// Назначение модуля: Сохраняет (отправляет на согласование) новый регламент
// Версия: 03102020
// Последние изменения: Начал
// 

	define('is_RUN', true);
	$ROOT_PATH = dirname(__FILE__);
    
	include_once("../../config.php");
	include_once("../../functions.php");
    
    $echolog = "";
    
    $User = isAuth();
    $post_results = print_r($User, true);

    if (!$User) 
    {
        exit ("Пользователь не авторизирован<br>");
    }


    $event = "";

    if (isset($_POST['rName']))         $rName = $_POST['rName'];
    if (isset($_POST['rOrganization'])) $rOrganization = $_POST['rOrganization'];
    if (isset($_POST['rRegNumber']))    $rRegNumber = $_POST['rRegNumber'];
    if (isset($_POST['rDescription']))  $rDescription = $_POST['rDescription'];


    $error = "";
    if (empty($rName))
    {
        $error .= "Не заполнено название регламента";
    }
    if (empty($rOrganization))
    {
        $error .= "Не заполнена организация";
    }
    /* и т.д. все проверки по полям*/

    if (!empty($error))
    {
        echo $error;
        exit();
    }
    
    else
    {

        $sql_insert = "
        INSERT INTO \"tReglament\"
        (\"uId\", \"rName\", \"rOrganization\", \"rRegNumber\", \"rDescription\") VALUES 
        (".$User['uId'].", '$rName', '$rOrganization', '$rRegNumber', '$rDescription')
        ";
        pg_query ($connection, $sql_insert) or die ("<small>Ошибка вставки <br>".$sql_insert."</small>");


        $query = "SELECT MAX(\"rId\") FROM \"tReglament\"";
  
        $result = pg_query ($connection, $query) or die ("Query failed<br>".$query);
        $row = pg_fetch_array($result);

        $rId = intval($row[0]);


            $result=pg_query($connection, "SELECT * FROM \"tReqRegulationsList\" WHERE \"rParentId\" = 0");

            $sql_ins_req = "";
            
            while ($row = pg_fetch_array($result)) // 
            { 
                $key = "req".$row['rId'];
                if (isset($_POST[$key]) && strlen($_POST[$key]) > 0)
                {   
                    $sql_ins_req.= "INSERT INTO \"tStructureReglament\"
                    (\"rId\", \"rreqid\", \"rtext\") VALUES
                    ($rId, '".$row['rId']."', '".$_POST[$key]."' );
                    ";
                }
                else
                {
                    $error .= "<b>Заполни:</b> ". $row['rTitle']."<br>";
                }

                $result2 = pg_query($connection, "SELECT * FROM \"tReqRegulationsList\" WHERE \"rParentId\" = ".$row['rId']);
                while ($row2 = pg_fetch_array($result2)) // 
                { 

                    $key2 = "req".$row2['rId'];
                    if (isset($_POST[$key2])  && strlen($_POST[$key2]) > 0)
                    {   
                        $sql_ins_req.= "INSERT INTO \"tStructureReglament\"
                        (\"rId\", \"rreqid\", \"rtext\") VALUES
                        ($rId, '".$row2['rId']."', '".$_POST[$key2]."' );
                        ";
                    }
                    else
                    {
                        $error .= "<b>Заполни:</b>". $row2['rTitle']."<br>";
                    }
                    
                }
            }        

            if (!empty($error))
            {
                echo "<small>$error</small>";
                exit();

            }
            else
            {
                pg_query ($connection, $sql_ins_req) or die ("<small>Ошибка вставки <br>".$sql_ins_req."</small>");    
            }
            


        /**/

        echo "<center>";
        echo "Регламент сохранен и отправлен на согласование";
        echo "</center>";        
        
    }

    

?>
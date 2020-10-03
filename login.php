<?php
// 
// Назначение модуля: Авторизая пользователя или выход из системы
// Версия: 03102020
// Последние изменения: Начал
// 


  define('is_RUN', true);
  $ROOT_PATH = dirname(__FILE__);

  include_once($ROOT_PATH."/config.php");
  include_once($ROOT_PATH."/functions.php");
  
// проверка имени/пароля и вход в систему
//echo   "event=".$_REQ['event'];

if(isset($_REQ['event']))
{
  if ($_REQ['event']=="regenter")
  {
    if (!isset($_REQ['name']) && !isset($_REQ['pass']))
    {
      exit ("<b>Ошибка авторизации</b>");
    }
    $name=iconv("UTF-8", $S_CONTENT_ENCODING, $_REQ['name']);
    $pass=iconv("UTF-8", $S_CONTENT_ENCODING, $_REQ['pass']);
    $url =iconv("UTF-8", $S_CONTENT_ENCODING, $_REQ['url']);

    //$name = "i.belonogov";

    $query = "SELECT * FROM \"tUsers\" WHERE \"uLogin\"='".$name."' AND \"uPassword\" = '".(md5($pass))."'";

    $result = pg_query ($connection, $query) or die ("Query failed<br>".$query);
    if (!$result)
    {
      
      $mess = "<font color='red'><b>Ошибка авторизации</b></font><br><br>
      Неправильный логин или пароль.<br>            ";
      echo RedirHtml("index.php?rand=".mt_rand(10000,99999), $mess, 2500);
      exit ;

    }

    $row = pg_fetch_array($result);

    
    if (isset($row['uId']))
    {
	     if ($row['uIsActive'] == 0)
	    {
	    	$mess = "<font color='red'><b>Ошибка авторизации</b></font><br><br>
	      Ваша учетная запись заблокирована!.<br>
	            ";
	      echo RedirHtml("index.php?rand=".mt_rand(10000,99999), $mess, 2500);
	      exit ;
	    }

	     if ($row['uIsActive'] == -14)
	    {
	    	$mess = "<font color='red'><b>Ошибка авторизации</b></font><br><br>
	      Ваша учетная запись заблокирована в связи с бездействием в течении 14 дней.<br>
	      Для разблокирования обратитесь к системному администратору.<br>
	            ";
	      echo RedirHtml("index.php?rand=".mt_rand(10000,99999), $mess, 2500);
	      exit ;
	    }

	   

	   
	    if ($row['uIsActive'] == -20)
	    {
	    	$mess = "<font color='red'><b>Ошибка авторизации</b></font><br><br>
	      Ваша учетная запись заблокирована в связи с истичение время действия.<br>
	      Для разблокирования обратитесь к системному администратору.<br>
	            ";
	      echo RedirHtml("index.php?rand=".mt_rand(10000,99999), $mess, 2500);
	      exit ;
	    }

	  $SessionKey = md5(rand(0,10000).$row['uLogin']);

      $wrfcookies = $row['uLogin']."|".$row['uPassword']."|".$row['usex']."|".$row['uId']."|".$SessionKey."|";
      setcookie($S_COOKIE, $wrfcookies, time() + 36000);
      
      $query = "UPDATE \"tUsers\" SET \"uLastVisit\"=".time().", \"uSessionKey\" = '$SessionKey',\"uIpAddress\"='".get_ip()."' WHERE \"uId\"=".$row['uId'];

      pg_query ($connection, $query) or die ("Query failed<br>".$query);

      $mess = "Здравствуйте, <b>".$row['uLogin']."</b>! <BR><BR> Вы успешно авторизировались в системе. В течении 30 секунд Вы будидет автоматически перенаправлены на главную страницу и сможете продолжить работу.<br><br>";
      echo RedirHtml("index.php?do=newreg&rand=".mt_rand(10000,99999), $mess, 2500);
      exit();
    }
    else
    {
      $mess = "<font color='red'><b>Ошибка авторизации</b></font><br><br>
      Неправильный логин или пароль.<br>
            ";
      echo RedirHtml("index.php?rand=".mt_rand(10000,99999), $mess, 10000);
      exit ;
    }
  }
}
if(isset($_REQ['event']))
{
  if ($_REQ['event']=="exit")
  {
    setcookie($S_COOKIE,"",time());
    $mess = "До скорого! Сейчас Вы будите автоматически перенаправлены на <a href='index.php?do=main'>главную страницу</a>.";
    //echo $mess;//

    echo RedirHtml("index.php?rand=".mt_rand(10000,99999), $mess, 2500);
    exit();//echo $wrfcookies;

  }
}

/**/

?>

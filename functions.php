<?php
// 
// Назначение модуля: Подключает все необходимые функциональные модули
// Версия: 30012020
// Последние изменения: начал
// 


/// Сразу зачищаем весь мусор в массивах и сделаем общий массив $_REQ

/* Защита от SQL инъекций */
foreach($_POST as $k => $v){$_POST[$k] = preg_replace('/ {2,}/',' ',trim(pg_escape_string($v)));}
foreach($_GET as $k => $v){$_GET[$k] = preg_replace('/ {2,}/',' ',trim(pg_escape_string($v)));}
/* Защита от SQL инъекций */

foreach($_REQUEST as $key => $value) {
  //$text_to_check = mysql_real_escape_string ($_REQUEST);
  $text_to_check = $_REQUEST;
  /*
  $text_to_check = strip_tags($text_to_check);
  $text_to_check = htmlspecialchars($text_to_check);
  $text_to_check = stripslashes($text_to_check);
  $text_to_check = addslashes($text_to_check);
  /**/
  ///$_REQ[$i] = $text_to_check;
  $$key = $text_to_check;
} $_REQ = $_REQUEST;
/// Все почистили и сохранили........................................
/**/

// Функция определяет запущен ли основной модуль или модули запускаются по отдельности
function isRun()
{
  if ( !defined('is_RUN') )
  {
    die('Hacking attempt');
  } else return true;
}


isRun(); // Проверили сами себя




////=======================================================================================================
////  Функция скачивает веб старницу, используется прокси
////=======================================================================================================
function get_page($url)
{

    if ($_SERVER['HTTP_HOST'] == "sudobase.local")
    {
      $ch = curl_init(); 
      $timeout = 5; // set to zero for no timeout 

      curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout); 
      curl_setopt ($ch, CURLOPT_URL, $url); 
      curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
      curl_setopt($ch, CURLOPT_PROXY, "http://10.0.1.101"); //your proxy url
      curl_setopt($ch, CURLOPT_PROXYPORT, "8080"); // your proxy port number 
      curl_setopt($ch, CURLOPT_PROXYUSERPWD, "i.belonogov:Cf[fkby65"); //username:pass 

      //curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; ru; rv:1.9.0.1) Gecko/2008070208');
      curl_setopt($ch, CURLOPT_ENCODING , "gzip");
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_HEADER, 0);

      $file_contents = curl_exec($ch); 
      curl_close($ch); 
      return $file_contents;
    }
    else
    {
      return file_get_contents($url);
    }
  
}

function getGeoPosition($address)
{
        global $apikey;
        $address = str_replace(" ", "+", $address);
        $address = "сахалинская+область+".$address;

        $xml_url  = "https://geocode-maps.yandex.ru/1.x/?apikey=".$apikey."&geocode=".urlencode($address);

        $xml_body = get_page($xml_url);

        $xml = simplexml_load_string($xml_body);
        $json = json_encode($xml);
        $array = json_decode($json, TRUE);

        
        $Formatted = $array['GeoObjectCollection']['featureMember']['GeoObject']['metaDataProperty']['GeocoderMetaData']['Address']['formatted'];
        $Component = $array['GeoObjectCollection']['featureMember']['GeoObject']['metaDataProperty']['GeocoderMetaData']['Address']['Component'];

        //var_dump($array['GeoObjectCollection']['featureMember']['GeoObject']);
        $pos =  $array['GeoObjectCollection']['featureMember']['GeoObject']['Point']['pos'];
        if (isset($Component))
        {
          for ($i=0; $i <= count($Component); $i++)
          {
            if ($Component[$i]['kind'] == "country") $country = $Component[$i]['name'];
            if ($Component[$i]['kind'] == "province") $province = $Component[$i]['name'];
            if ($Component[$i]['kind'] == "area") $area = $Component[$i]['name'];
            if ($Component[$i]['kind'] == "locality") $locality = $Component[$i]['name'];
            if ($Component[$i]['kind'] == "street") $street = $Component[$i]['name'];
            if ($Component[$i]['kind'] == "house") $house = $Component[$i]['name'];
          }
          //echo $row['pId'].": ".$row['pAddressSakh']."==>".$country.", ".$province.", ".$area.", ".$locality.", ".$street.", ".$house."<br>";
        }
        //echo $Formatted."\r\n";

        $position = $arrayName = array('address' => $Formatted, 'position' => $pos);
        return $position;
}

// Функция вписывает в журнал время и место изменения БД
function MaxId($TableName, $TableKey="")
{
    global $connection; 

    if ($TableName == "tMedAnalyze") $TableKey = "maId";
    
    if ($TableName == "tMedView") $TableKey = "mvId";

    if ($TableName == "tMedVisit") $TableKey = "mvId";
    if ($TableName == "tModeError") $TableKey = "meId";
    if ($TableName == "tPeople") $TableKey = "pId";
    if ($TableName == "tPeopleMoving") $TableKey = "pmId";
    if ($TableName == "tUsers") $TableKey = "uId";
    if ($TableName == "tFiles") $TableKey = "fId";
    if ($TableName == "tPrecept") $TableKey = "prId";
    if ($TableName == "tGeoCode") $TableKey = "gId";

    if ($TableName == "tObservatory") $TableKey = "oId";
    if ($TableName == "tLPU") $TableKey = "lpuId";

    if ($TableName == "tEventList") $TableKey = "elId";
    if ($TableName == "tContacts") $TableKey = "cId";
    if ($TableName == "tEvents") $TableKey = "eId";

    $query = "SELECT MAX(\"$TableKey\") FROM \"$TableName\"";
  
    $result = pg_query ($connection, $query) or die ("Query failed<br>".$query);
    $row = pg_fetch_array($result);
    return intval($row[0]);
}


// Функция проверяет афторизирован пользователь или нет, если авторизирован возвращает массив о пользователе
function isAuth()
{
   /// Взяли куки, взяли префикс БД, открытое соединене с БД
   global $S_COOKIE, $_pref, $connection;

   // ищем В КУКАХ чтобы определить ИМЯ
   if (isset($_COOKIE[$S_COOKIE]))
   {
     $wrfc  = $_COOKIE[$S_COOKIE];
     $wrfc  = explode("|", $wrfc);
     
     $_UNAME=$wrfc[0];      
     $_UPASS=$wrfc[1];
     $_UPOLL=$wrfc[2];     
     $_UIDDD=$wrfc[3];
     $_SESKEY=$wrfc[4];

     if (empty($_UIDDD)) $_UNAME = -1;
     if (empty($_UPERS)) $_UPERS = -1;

     //$query  = "SELECT * FROM \"tUsers\", \"svCompany\" WHERE \"svCompany\".\"cId\"=\"tUsers\".\"uCompanyId\" AND \"uLogin\"='".$_UNAME."' AND \"uId\" = ".$_UIDDD." AND \"uPassword\"='".$_UPASS."'";
     $query  = "SELECT * FROM \"tUsers\" WHERE \"uLogin\"='".$_UNAME."' AND \"uSessionKey\"='".$_SESKEY."'";
     $result = pg_query ($connection, $query) or die ("Query failed<br>".$query);
     $row    = pg_fetch_array($result);

     if (!isset($row['uId']))
     {
       return false;      
     }


     $query  = "SELECT * FROM \"tUsers\" WHERE \"uLogin\"='".$_UNAME."' AND \"uId\" = ".$_UIDDD." AND (\"uIsActive\" = 1 OR \"uIsActive\" = -1 ) AND \"uPassword\"='".$_UPASS."'  ";

     $result = pg_query ($connection, $query) or die ("Query failed<br>".$query);
     $row    = pg_fetch_array($result);

     if (!isset($row['uId']))
     {
       return false;      
     }
     
     $query = "UPDATE \"tUsers\" SET \"uLastVisit\"=".time().", \"uIpAddress\"='".get_ip()."' WHERE \"uId\"=".$row['uId'];
     pg_query ($connection, $query) or die ("Query failed<br>".$query);

     return $row;
     if (!$row)
     {
       return false;
     }
   }
   else
   {
     return false;
   }
   return false;
}

// Функция определяет реальный ip пользователя
function get_ip()
{
  if(isset($HTTP_SERVER_VARS)) 
  {
    if(isset($HTTP_SERVER_VARS["HTTP_X_FORWARDED_FOR"])) 
    {
      $realip = $HTTP_SERVER_VARS["HTTP_X_FORWARDED_FOR"];
    }
    elseif(isset($HTTP_SERVER_VARS["HTTP_CLIENT_IP"])) 
    {
      $realip = $HTTP_SERVER_VARS["HTTP_CLIENT_IP"];
    }
    else
    {
      $realip = $HTTP_SERVER_VARS["REMOTE_ADDR"];
    }
  } else
  {
    if(getenv( 'HTTP_X_FORWARDED_FOR' ) ) 
    {
      $realip = getenv( 'HTTP_X_FORWARDED_FOR' );
    }
    elseif ( getenv( 'HTTP_CLIENT_IP' ) ) 
    {
      $realip = getenv( 'HTTP_CLIENT_IP' );
    }
    else 
    {
      $realip = getenv( 'REMOTE_ADDR' );
    }
  }
  return $realip;
}




// Функция вписывает в журнал время и место изменения БД
function ModeBase($TableName, $WhoIs, $Oper, $TableKey="0")
{
  /// Взяли куки, взяли префикс БД, открытое соединене с БД
  global $connection;

  $data = date("Y-m-d H:i:s", time());
  $query = "INSERT INTO \"tHistory\" (\"hTableName\", \"hModTime\", \"hWhoIsId\", \"hOperation\", \"hTableKey\", \"hIpAddress\") VALUES ('$TableName', '$data', ".$WhoIs['uId'].", '$Oper',
  $TableKey, '".get_ip()."')";
  
  pg_query ($connection, $query) or die ("Query failed<br>".$query);
  
}



// Класс для работы с шаблонами
class parse_class
{
  var $vars     = array();
  var $template;

  function get_tpl($tpl_name)
  {
    if(empty($tpl_name) || !file_exists($tpl_name))
    {
      return false;
    }
    else
    {
      $this->template  .= file_get_contents($tpl_name);
    }
  }

  function set_empty_tpl()
  {
  	$this->template = "";
  }

  function set_tpl($key,$var)
  {
    $this->vars[$key] = $var;
  }

  function tpl_parse()
  {
    foreach($this->vars as $find => $replace)
    {
      $this->template = str_replace("{".$find."}", $replace, $this->template);
    }
  }
}


// Функция пишет заголовочные файлы для страницы
function makeMeta($keywords="",$description="",$copyright="")
{
  return "
    <meta http-equiv=\"content-style-type\" content=\"text/css\" />
    <meta http-equiv=\"content-language\" content=\"ru-ru\" />
    <meta http-equiv=\"imagetoolbar\" content=\"no\" />
    <meta name=\"resource-type\" content=\"document\" />
    <meta name=\"distribution\" content=\"global\" />
    <meta name=\"copyright\" content=\"".$copyright."\" />
    <meta name=\"keywords\" content=\"".$keywords."\" />
    <meta name=\"description\" content=\"".$description."\" /> ";
}


function makeTitle()
{
  return "Информационная система \"РЕГЛАМЕНТ\"";
}  



if(!function_exists('mime_content_type')) {

    function mime_content_type($filename) {

        $mime_types = array(

            'txt' => 'text/plain',
            'htm' => 'text/html',
            'html' => 'text/html',
            'php' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'xml' => 'application/xml',
            'swf' => 'application/x-shockwave-flash',
            'flv' => 'video/x-flv',

            // images
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'JPG' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'JPEG' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',

            // archives
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            'exe' => 'application/x-msdownload',
            'msi' => 'application/x-msdownload',
            'cab' => 'application/vnd.ms-cab-compressed',

            // audio/video
            'mp3' => 'audio/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',

            // adobe
            'pdf' => 'application/pdf',
            'PDF' => 'application/pdf',
            'psd' => 'image/vnd.adobe.photoshop',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            'ps' => 'application/postscript',

            // ms office
            'doc' => 'application/msword',
            'rtf' => 'application/rtf',
            'xls' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',

            // open office
            'odt' => 'application/vnd.oasis.opendocument.text',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        );

        $ext = strtolower(array_pop(explode('.',$filename)));
        if (array_key_exists($ext, $mime_types)) {
            return $mime_types[$ext];
        }
        elseif (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME);
            $mimetype = finfo_file($finfo, $filename);
            finfo_close($finfo);
            return $mimetype;
        }
        else {
            return 'application/octet-stream';
        }
    }
}




function smtpmail($to='', $mail_to, $subject, $message, $headers='') 
{
  global $config;
  $SEND = "Date: ".date("D, d M Y H:i:s") . " UT\r\n";
  $SEND .= 'Subject: =?'.$config['smtp_charset'].'?B?'.base64_encode($subject)."=?=\r\n";
  if ($headers) $SEND .= $headers."\r\n\r\n";
  else
  {
      $SEND .= "Reply-To: ".$config['smtp_username']."\r\n";
      $SEND .= "To: \"=?".$config['smtp_charset']."?B?".base64_encode($to)."=?=\" <$mail_to>\r\n";
      $SEND .= "MIME-Version: 1.0\r\n";
      $SEND .= "Content-Type: text/html; charset=\"".$config['smtp_charset']."\"\r\n";
      $SEND .= "Content-Transfer-Encoding: 8bit\r\n";
      $SEND .= "From: \"=?".$config['smtp_charset']."?B?".base64_encode($config['smtp_from'])."=?=\" <".$config['smtp_username'].">\r\n";
      $SEND .= "X-Priority: 3\r\n\r\n";
  }
  $SEND .=  $message."\r\n";
   if( !$socket = fsockopen($config['smtp_host'], $config['smtp_port'], $errno, $errstr, 30) ) {
    if ($config['smtp_debug']) echo $errno."<br>".$errstr;
    return false;
   }
 
  if (!server_parse($socket, "220", __LINE__)) return false;
 
  fputs($socket, "HELO " . $config['smtp_host'] . "\r\n");
  if (!server_parse($socket, "250", __LINE__)) {
    if ($config['smtp_debug']) echo '<p>Не могу отправить HELO!</p>';
    fclose($socket);
    return false;
  }
  
  /* Несколько проверок далее не используются, т.к. почтовик не требует аутентификацию
  fputs($socket, "AUTH LOGIN\r\n");
  if (!server_parse($socket, "334", __LINE__)) {
    if ($config['smtp_debug']) echo '<p>Не могу найти ответ на запрос авторизаци.</p>';
    fclose($socket);
    return false;
  }
  /**/
  /*
  fputs($socket, base64_encode($config['smtp_username']) . "\r\n");
  if (!server_parse($socket, "334", __LINE__)) {
    if ($config['smtp_debug']) echo '<p>Логин авторизации не был принят сервером!</p>';
    fclose($socket);
    return false;
  }
  /**/
  /*
  fputs($socket, base64_encode($config['smtp_password']) . "\r\n");
  if (!server_parse($socket, "235", __LINE__)) {
    if ($config['smtp_debug']) echo '<p>Пароль не был принят сервером как верный! Ошибка авторизации!</p>';
    fclose($socket);
    return false;
  }
  /**/
  fputs($socket, "MAIL FROM: <".$config['smtp_username'].">\r\n");
  if (!server_parse($socket, "250", __LINE__)) {
    if ($config['smtp_debug']) echo '<p>Не могу отправить комманду MAIL FROM: </p>';
    fclose($socket);
    return false;
  }
  fputs($socket, "RCPT TO: <" . $mail_to . ">\r\n");
 
  if (!server_parse($socket, "250", __LINE__)) {
    if ($config['smtp_debug']) echo '<p>Не могу отправить комманду RCPT TO: </p>';
    fclose($socket);
    return false;
  }
  fputs($socket, "DATA\r\n");
 
  if (!server_parse($socket, "354", __LINE__)) {
    if ($config['smtp_debug']) echo '<p>Не могу отправить комманду DATA</p>';
    fclose($socket);
    return false;
  }
  fputs($socket, $SEND."\r\n.\r\n");
 
  if (!server_parse($socket, "250", __LINE__)) {
    if ($config['smtp_debug']) echo '<p>Не смог отправить тело письма. Письмо не было отправленно!</p>';
    fclose($socket);
    return false;
  }
  fputs($socket, "QUIT\r\n");
  fclose($socket);
  return TRUE;
}
 
function server_parse($socket, $response, $line = __LINE__) {
  global $config;
  while (@substr($server_response, 3, 1) != ' ') {
    if (!($server_response = fgets($socket, 256))) {
      if ($config['smtp_debug']) echo "<p>Проблемы с отправкой почты!</p>$response<br>$line<br>";
      return false;
    }
  }
  if (!(substr($server_response, 0, 3) == $response)) {
    if ($config['smtp_debug']) echo "<p>Проблемы с отправкой почты!</p>$response<br>$line<br>";
    return false;
  }
  return true;
}



function RedirHtml($url, $mess, $t)
{
    $print = "<html><head><link rel='stylesheet' href='style.css' type='text/css'></head><body>
    <br><br><br><br><br>
    <script language='Javascript'>function reload() {location = \"".$url."\"}; setTimeout('reload()', ".$t.");</script>
    <center><br><br><br><br><br><br>

    <table width='900' align='center' border='1' cellpadding='5' cellspacing='5' bgcolor='#f2f2f2'>
        <tr>
          <td align='center'>
            <center>
            <br><br>".$mess."
              <br><br><br><br>
              <a href='".$url."'>Нажмите здесь, если автоматическая переадресация не происходит ➦</a>
              <br><br><br><br>
            
            </center>
        </td>
    </tr>
    </table>
    </center>

    <p>&nbsp;&nbsp;</p>    <p>&nbsp;&nbsp;</p>    <p>&nbsp;&nbsp;</p>    <p>&nbsp;&nbsp;</p>    <p>&nbsp;&nbsp;</p>    
    </body></html>";
    return  $print;
}



?>
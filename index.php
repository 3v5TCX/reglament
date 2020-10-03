<?php
// 
// Назначение модуля: Основной модуль (роутер)
// Версия: 03102020
// Последние изменения: Полная переработка GUI (подключил)
// 

  define('is_RUN', true);
  $ROOT_PATH = dirname(__FILE__);

  include_once($ROOT_PATH."/config.php");
  include_once($ROOT_PATH."/functions.php");

  $parse = new parse_class;
  $parse->get_tpl($ROOT_PATH."/modules/template/overall_header.tpl");

  $do = "";
  if (isset($_GET['do']))  $do = $_GET['do'];  // определяем что делать и какой модуль подключать
  
  $User = isAuth();   // проверяем пользователь авторизирован или нет, если нет то $do = login
  
  if (!$User) $do="login";

  if ($User['uIsActive'] == '-1') // Если пользователь новенький, отправляем его на смену пароля
  {
      $do = "1chpassword";
  }

  switch ($do) 
  {

    case 'delcontact':  // эпидокружение
    {
      $parse->get_tpl($ROOT_PATH."/modules/template/delcontact.tpl"); //открываем нужный шаблон
      include_once($ROOT_PATH."/modules/php/delcontact.php");
      break;
    }    
    
    case '1chpassword': // пользователь отправлен на страницу смены пароля
    {  

      if (isset($_POST['event']) && $_POST['event'] == 'set')
      {

        if (isset($_POST['passw1'])) $passw1 = trim($_POST['passw1']);
        if (isset($_POST['passw2'])) $passw2 = trim($_POST['passw2']);

        if ($passw1 != $passw2)
        {
          $mess = "<font color='red'>Ошибка, <b>".$User['uLogin']."</b>! <BR><BR> Пароли не совпали!</font><br><br>";
          echo RedirHtml("index.php?do=1chpassword&rand=".mt_rand(10000,99999), $mess, 10000);
          exit();            
        }

        if (strlen($passw1) < 6 )
        {
          $mess = "<font color='red'>Ошибка, <b>".$User['uLogin']."</b>! <BR><BR> Пароль должен быть больше 6 символов</font><br><br>";
          echo RedirHtml("index.php?do=1chpassword&rand=".mt_rand(10000,99999), $mess, 10000);
          exit();            
        }


        $sql = "UPDATE \"tUsers\"  SET \"uPassword\" = MD5('$passw1'), \"uIsActive\" = 1 WHERE \"uId\" = ".$User['uId'];
        pg_query ($connection, $sql) or die ("Ишибка установки нового пароля".$sql);
        
        $wrfcookies = $User['uLogin']."|".md5($passw1)."|".$row['usex']."|".$User['uId']."|";
        setcookie($S_COOKIE, $wrfcookies, time() + 3600);        


        $mess = "Отлично, <b>".$User['uLogin']."</b>! <BR><BR> Ваш новый пароль установлен. В течении 30 секунд Вы будидет автоматически перенаправлены на главную страницу и сможете продолжить работу.<br><br>";
        echo RedirHtml("index.php?do=search&rand=".mt_rand(10000,99999), $mess, 2500);
        exit();


      }
      $parse->get_tpl($ROOT_PATH."/modules/template/1chpassword.tpl"); //открываем нужный шаблон
      include_once($ROOT_PATH."/modules/php/1chpassword.php");
      break;
    }

    case 'login': // пользователь отправлен на авторизацию 
    {  
      include_once("login.php");
      $parse->get_tpl($ROOT_PATH."/modules/template/login_body.tpl"); //открываем нужный шаблон
      break;
    }

    
    case 'newreg': //новый регламент
    {

      $parse->get_tpl($ROOT_PATH."/modules/template/newreg_body.tpl"); //открываем нужный шаблон
      include_once($ROOT_PATH."/modules/php/newreg_body.php");
      break;
    }





    /**/
    
    default: //по умолчанию главная страница (рабочий стол госслуж)
    {
      $parse->get_tpl($ROOT_PATH."/modules/template/all_list.tpl"); 	//открываем нужный шаблон
      include_once($ROOT_PATH."/modules/php/all_list.php");			//подключаем нужный модуль
      break;
    }

  }

  $parse->set_tpl("META", makeMeta()); //устанавливаем в заголовке страницы все метаданные

  // авторизирован пользователи или нет
  if ($User)  $parse->set_tpl("AUHT_USER_NAME", "<script>
        function confirm_exit(){
            \$.messager.confirm('Подтверждаю', 'Вы уверены, что нужно выйти из системы?', function(r){
                if (r){
                    window.location.href = \"./index.php?do=login&event=exit\";
                    ;
                }
            });
        }
  </script><a href=\"#\" class=\"easyui-linkbutton\" onclick=\"confirm_exit();\">Выйти</a>");
  
  else $parse->set_tpl("AUHT_USER_NAME", "<a href=\"./index.php?do=login\" class=\"easyui-linkbutton\">Войти</a>");




  // 
  // работа с шаблоном (заменяем все известне теги)
  //



  // авторизирован пользователи или нет
  if ($User)  $parse->set_tpl("AUHT_USER_EXIT", "<script>
        function confirm_exit(){
            \$.messager.confirm('Подтверждаю', 'Вы уверены, что нужно выйти из системы?', function(r){
                if (r){
                    window.location.href = \"./index.php?do=login&event=exit\";
                    ;
                }
            });
        }
  </script><a href=\"#\" class=\"easyui-linkbutton\" onclick=\"confirm_exit();\">Выйти</a>");
  
  else $parse->set_tpl("AUHT_USER_EXIT", "<a href=\"./index.php?do=login\" class=\"easyui-linkbutton\">Войти</a>");

  $parse->set_tpl("AUHT_ADMIN", $AUHT_ADMIN); 

  $parse->set_tpl("SYNC_TIME", $SYNC_TIME); 
  $parse->set_tpl("SITENAME", $S_SITENAME); 
  $parse->set_tpl("PAGE_TITLE", $S_PAGETITLE); 
  $parse->set_tpl("LAST_VISIT_DATE", date("d.m.Y",$User['ulastvisit']) ); 
  $parse->set_tpl("CURRENT_TIME",date("d.m.Y",time()));
  //$parse->set_tpl("AUHT_USER_EXIT", "");
  $parse->set_tpl("U_INDEX", "index.php?do=main&rand=".md5(mt_rand(0,999999)));
  $parse->get_tpl($ROOT_PATH."/theme/".$S_THEME."/overall_footer.tpl");
  $parse->set_tpl("CONTENT_ENCODING",$S_CONTENT_ENCODING);
  $parse->set_tpl("THEME_PATH",$S_MAIN_URL."/theme/".$S_THEME);
  
  $DTime = date("m.d.Y H:i:s",time() );
  $parse->set_tpl("DATA_TIME",$DTime);
  $parse->set_tpl("AUHT_USER_NAME",$User['uLogin']." (".$User['uCompany'].")");
  $parse->set_tpl("IP_ADDRESS",$User['uIpAddress']);
  

  $SPRAVLIST = "";

  if ($User['uStatus']==60)
  {
    $SPRAVLIST = "<a href=\"#\" class=\"easyui-linkbutton\" onclick=\"window.location.href='index.php?do=spravlist&event=view'\">Справочники</a>";
  }

  $USERLIST = "";
  if ($User['uStatus']==60)
  {
    $USERLIST = "<a href=\"#\" class=\"easyui-linkbutton\" onclick=\"window.location.href='index.php?do=userlist&event=view'\">Пользователи</a>";
  }

  $parse->set_tpl("SPRAVLIST",$SPRAVLIST);
  $parse->set_tpl("USERLIST",$USERLIST);


  // Кто в системе он-лайн сечас
  $ONLINE = "";

  
  $ONLINE.=".";
  $ONLINE = "<br><small><b>Он-лайн:</b> ".$ONLINE."</small></font>";
  if ($User["uId"] !=1 ) $ONLINE = "";
  $parse->set_tpl("ONLINE",$ONLINE);

  
  $NEWPEOPLEBUTTON = "";
  if ($User['uStatus']!=5)
  {
    $NEWPEOPLEBUTTON = "<a href=\"#\" class=\"easyui-linkbutton\" onclick=\"window.location.href='index.php?do=people&event=new'\">Новый гражданин</a>";
  }

  $parse->set_tpl("NEWPEOPLEBUTTON",$NEWPEOPLEBUTTON);

  $parse->set_tpl("QSEARCH",$QSEARCH);    


  $parse->get_tpl($ROOT_PATH."/modules/template/overall_footer.tpl");  
  $parse->tpl_parse(); //парсим шаблон
  echo $parse->template; //печатем все что получилось
?>


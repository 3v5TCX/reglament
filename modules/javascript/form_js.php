<?php

define('is_RUN', true);
$ROOT_PATH = dirname(__FILE__);
include_once("../../config.php");
include_once("../../functions.php");


$User = isAuth();
if (!$User) exit ("Нет логина");

$cid = $_REQ['cid'];  // определяем форму

if ($User['uStatus']=='user') //проверка, принадлежит ли форма пользователю или нет
    $query = "SELECT * FROM \"svUserForms\" WHERE \"fUserId\"=".$User['uId']." AND \"ufId\" = ".$cid;

if ($User['uStatus']=='operator')  //оператору не требуется проверка, что форма принадлежит ему
    $query = "SELECT * FROM \"svUserForms\" WHERE \"ufId\" = ".$cid;

$result = pg_query ($connection, $query) or die ("Query failed<br>".$query);
if (!$result)
{
    exit ("<b>Ошибка авторизации</b><br>");
}

$row = pg_fetch_array($result);

$form = $row['fHtmlTemplate'];

if ($User['uStatus']=='operator')
{

    $cquery  = "SELECT * FROM \"svUsers\", \"svCompany\" WHERE \"svCompany\".\"cId\"=\"svUsers\".\"uCompanyId\" AND \"uId\" = ".$row['fUserId'];
    
    //if ()
    //$cquery  = "SELECT * FROM \"svUsers\", \"svCompany\" WHERE \"svCompany\".\"cId\"=\"svUsers\".\"uCompanyId\" AND \"uId\" = ".$row['fUserId'];

    $cresult = pg_query ($connection, $cquery) or die ("Query failed<br>".$cquery);
    $company = pg_fetch_array($cresult);

    echo "
    <center>  
          <table class='ftb' cellspacing='0' border='1' width='100%'>
            <tr class='ftb'>
              <td colspan='2' class='ftb' align='center'>
                <h3>".$company['cName']."</h3>        
              </td>
            </tr>
            <tr class='ftb'>
              <td class='ftb' width='40%'>
                Код предприятия (ОКПО):
              </td>
              <td class='ftb' width='60%' align='center'>
                ".$company['cOKPO']."
              </td>
            </tr>
            <tr class='ftb'>
              <td class='ftb' width='40%'>
                Руководитель организации (ФИО):
              </td>
              <td class='ftb' width='60%' align='center'>
                ".$company['cLeaderFIO']."
              </td>
            </tr>
            <tr class='ftb'>
              <td class='ftb' width='40%'>
                Должностное лицо, ответственное за составление формы (должность):
              </td>
              <td class='ftb' width='60%' align='center'>
                ".$company['cResponsiblePost']."
              </td>
            </tr>
            <tr class='ftb'>
              <td class='ftb' width='40%'>
                Должностное лицо, ответственное за составление формы (ФИО):
              </td>
              <td class='ftb' width='60%' align='center'>
                ".$company['cResponsibleFIO']."
              </td>
            </tr>
            <tr class='ftb'>
              <td class='ftb' width='40%'>
                Контактный телефон:
              </td>
              <td class='ftb' width='60%' align='center'>
                ".$company['cPhone']."
              </td>
            </tr>
            <tr class='ftb'>
              <td class='ftb' width='40%'>
                Эл.почта:
              </td>
              <td class='ftb' width='60%' align='center'>
                ".$company['cEmail']."
              </td>
            </tr>
          </table>
      </center>
    <hr>";
}


echo "
<h3 style='color:darkblue'>Контрольный срок сдачи: ".date("d.m.Y", $row['fControlTime'])."</h3>

<form id='ff' action='./modules/php/form_save.php' method='post'>";

$form = explode("\n", $form);
$k = 0;
$form_data = json_decode($row['fData'], true);
for ($i=0;  $i < count($form);  $i++)
{
    $formstr = $form[$i];
    if (strpos($formstr, "{INP}") !=0 )
    {
        $k++;
        if (($row['fStatus']=="new" && $User['uStatus']=="user") || ($row['fStatus']=="validate" && $User['uStatus']=="operator"))
        {
            if (isset($form_data['CELL'.$k]))
            {
                $formstr = str_replace("{INP}", "<input class='ftb' type='text' value='".$form_data['CELL'.$k]."' name='CELL".$k."' style='border-style:none; width: 98%; height: 32px; text-align:center;'>", $form[$i]);
            }
            else
            {
                $formstr = str_replace("{INP}", "<input class='ftb' type='text' value='' name='CELL".$k."' style='border-style:none; width: 98%; height: 32px; text-align:center;'>", $form[$i]);
            }
        }
        else 
        {
            $formstr = str_replace("{INP}", "<center>&nbsp;".$form_data['CELL'.$k]."&nbsp;</center>", $form[$i]);
            //$formstr = str_replace("{INP}", $row['fStatus']."<br>".$User['uStatus'], $form[$i]);
        }
    }
    echo $formstr;
}

    if ($User['uStatus']=="user")
    {
        $OPERATOR_HTM = "
        <option value='save'>Сахранить как черновик без отправки</option>
        <option value='saveandsend'>Сахранить и отправить на проверку</option>";
    }

    if ($User['uStatus'] == "operator")
    {
        $OPERATOR_HTM = "
        <option value='saveandsoglas'>Согласовать</option>
        <option value='save'>Отправить на доработку</option>
        <option value='saveandsend'>Сахранить без согласования</option>";
    }

$from_html = "
    <center>
    <input type='hidden' name='cid' value='".$cid."'>
    <hr>
    <h3 align='center'>Действия с формой</h3>
        <select name='doform' style='width:300px;'>
            ".$OPERATOR_HTM."
        </select>
        </form>
        <br><br>

        <a href=\"javascript:void(0)\" class=\"easyui-linkbutton\" onclick=\"clearForm()\" style=\"width:150px\"> Отчистить </a>
        <a href=\"javascript:void(0)\" class=\"easyui-linkbutton\" onclick=\"submitForm()\" style=\"width:150px\"> Выполнить </a>    
        <script>
            function submitForm(){
                $('#ff').form('submit');
            }
            function clearForm(){
                $('#ff').form('clear');
            }
        </script>    

        <script type='text/javascript'>
            $(function(){
                $('#ff').form({
                    success:function(data){
                        $.messager.alert('Info', data, 'info');
                    }
                });
            });
        </script>
    ";

if ($row['fStatus'] == 'new' && $User['uStatus'] == 'user')
{
    echo $from_html;
}

if ($row['fStatus'] == 'validate' && $User['uStatus'] == 'user')
{
    echo "<hr><h3 align='center'>Форма на проверке</h3>";
}

if ($row['fStatus'] == 'validate' && $User['uStatus'] == 'operator')
{
    echo $from_html;
}


?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset={CONTENT_ENCODING}">
    <meta http-equiv="Content-Style-Type" content="text/css">
    {META}
    <title>:: {PAGE_TITLE}</title>
    
    <link rel="stylesheet" type="text/css" href="./repo/easyui/jquery-easyui-1.9.4/themes/material-blue/easyui.css">
    <link rel="stylesheet" type="text/css" href="./repo/easyui/jquery-easyui-1.9.4/themes/icon.css">
    <link rel="stylesheet" type="text/css" href="./repo/easyui/jquery-easyui-1.9.4/themes/color.css">
    <link rel="stylesheet" type="text/css" href="./repo/easyui/jquery-easyui-1.9.4/demo/demo.css">

    <script type="text/javascript" src="./repo/jquery/1.9.1/jquery-1.9.1.min.js"></script>
    <script type="text/javascript" src="./repo/easyui/jquery-easyui-1.9.4/jquery.easyui.min.js"></script>
    <link rel="stylesheet" type="text/css" href="./repo/xcal/xcal.css">
    <script type="text/javascript" src="./repo/xcal/xcal.js"></script>
	
    <style type="text/css">
    table.ftb {
        font-family: "Lucida Sans Unicode", "Lucida Grande";
        font-size: 14px;
        border-collapse: collapse;
    }
    td.ftb {
        border-style: solid;
        border-width: 0 1px 1px 0;
        background: #FFFFFF;
    }
    tr.ftb, td.ftb:hover 
    {
        background: #D1E6F0;
    }
    input[type="text"]:hover
    {
        background:#f0f0f0;
        cursor:crosshair;
    }
    </style>

    <script>
        function topRight(){
            $.messager.show({
                title:'Важное сообщение!',
                msg:'<font color=green><b>Сервер работает!</b></font>',
                showType:'show',
                style:{
                    left:'',
                    right:0,
                    top:document.body.scrollTop+document.documentElement.scrollTop,
                    bottom:''
                }
            });
        }        
    </script>


  </head>
  <body class="easyui-layout" style="text-align:left" onload = "topRight()">
  <div region="north" border="false" style="height:120px; background: #343d46; color: #ffffff;">
    <table width="100%" border="0">
      <tr>
        <td>&nbsp;&nbsp;&nbsp;<img src='./images/gerb_w_32x36.png' width="32" height="36">&nbsp;&nbsp;&nbsp;</td>
        <td width="70%">        
          <br>
          <font color="white">ИС "РЕГЛАМЕНТ" 
            {ONLINE}
            <p>

            <a href="#" class="easyui-linkbutton" onclick="window.location.href='index.php?do=newreg'">Новый регламент</a>
            <a href="#" class="easyui-linkbutton" onclick="window.location.href='index.php?do=search'">Поиск</a>
            

            &nbsp;&nbsp;
            {SPRAVLIST}
            {USERLIST}
            &nbsp;&nbsp;
            {AUHT_USER_EXIT}
          
            {QSEARCH}            
            </p>                
        </td>
        
        <td width="30%" align="right">
            <font color='white'>
                <b>{AUHT_USER_NAME}</b><br>
                <small>{DATA_TIME}</small><br>
                <small>{IP_ADDRESS}</small><br>
            </font>
        </td>        
      </tr>
    </table>
  </div>
  
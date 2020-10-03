
    <div region='center'>
      <center>  
        <br><br><br>
          <table class='ftb' cellspacing='0' border='2' width='95%' bgcolor='#f1f1f1' bordercolor = "#d1e1e1">
            <tr class='ftb'>
              <th class='ftb' align='center' colspan='3'>
                
                <table width='100%'>
                  <tr>
                    <td width='33%' align='left'>
                    </td>
                    <td width='34%' align='center'>
                      <font size='5'><b>Новый регламент</b></font><br>
                    </td>
                    <td width='33%' align='right'>
                    </td>
                  </tr>
                </table>
              </td>
            </th>
            <tr>
              <td colspan='3' align="center">

                <table width="100%">
                  <tr>
                    <td valign="top">
          <!--------------------------------------------------------------------------------------->

          <form id='companyform' method='post' action='./modules/php/newreg_create.php'>
          <input type="hidden" name="pid" value="{PID}">
          <table class='ftb' cellspacing='0' border='1' width='98%'>
            <tr class='ftb'>
              <td class='ftb' width='40%'>
                Название регламента:
              </td>
              <td class='ftb' width='60%' align='center'>
                <input class='ftb' type='text' value='{rName}' name='rName' style='border-style:none; width: 95%; height: 32px; text-align:center;' >
              </td>
            </tr>
            <tr class='ftb'>
              <td class='ftb' width='40%'>
                Организация (ОИВ):
              </td>
              <td class='ftb' width='60%' align='center'>
                <input class='ftb' type='text' value='{rOrganization}' name='rOrganization' style='border-style:none; width: 95%; height: 32px; text-align:center;' >
              </td>
            </tr>
            <tr class='ftb'>
              <td class='ftb' width='40%'>
                Номер регламента:
              </td>
              <td class='ftb' width='60%' align='center'>
                <input class='ftb' type='text' value='{rRegNumber}' name='rRegNumber' style='border-style:none; width: 95%; height: 32px; text-align:center;' >
              </td>
            </tr>


            <tr class='ftb'>
              <td class='ftb' width='40%'>
                Краткое описание:
              </td>
              <td class='ftb' width='60%' align='center'>
                
                <input class='ftb' type='text' value='{rDescription}' name='rDescription' style='border-style:none; width: 95%; height: 32px; text-align:center;' >
              </td>
            </tr>


          </table>


          <p>
            {LISTREQ}
          </p>


              
          
          <a href='javascript:void(0)' class='easyui-linkbutton' onclick='submitForm()' style='width:150px'> Сохранить / Отправить на согласование</a>    
          <script>
                  function submitForm(){
                      $('#companyform').form('submit');
                  }
                  function clearForm(){
                      $('#companyform').form('clear');
                  }
          </script>    

          <script type='text/javascript'>
                  $(function(){
                      $('#companyform').form({
                          success:function(data){
                              $.messager.alert('Info', data, 'info');
                          }
                      });
                  });
          </script>


          </form>
              
                    </td>
                  </tr>
                </table>
          <hr>
          

              </td>
            </tr>
            
            
          </table>

          

          </table>  
          
          <br><br>
      </center>  
    </div>

    

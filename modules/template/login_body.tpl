    <script>
        function clearForm(){
            $('#loginform').form('clear');
        }
    </script>

    <div id="win" class="easyui-window" title="Вход в систему" style="width:400px;" data-options="iconCls:'icon-man',modal:true">
        <form id="loginform" style="padding:10px 40px;" action="index.php?do=login" method="post">
            <input type="hidden" name="event" value="regenter">
            <div style="margin-top:20px"><input class="easyui-textbox" data-options="iconCls:'icon-man'" label="Login:" style="width:100%" name="name"></div>
            <div style="margin-top:20px"><input class="easyui-passwordbox" label="Pass:" style="width:100%" name="pass"></div>
            <div style="margin-top:20px;padding:5px;text-align:center;">
            <input type="submit" value="&nbsp;&nbsp;&nbsp;Войти&nbsp;&nbsp;&nbsp;" class="l-btn l-btn-text" class="easyui-linkbutton" icon="icon-cancel">            
            <a href="javascript:void(0)" class="easyui-linkbutton" icon="icon-cancel" onclick="clearForm()">Отмена</a>
            </div>
        </form>
    </div>
<?php
ini_set("track_errors", 1);
header('Content-Type: text/html; charset=utf-8');

require_once(dirname(__FILE__)."/../control/UserDAO.php");

if(!empty($_POST['login']) && !empty($_POST['senha']))
{
   if(!isset($_SESSION))
   {
      session_start();
   }
   
   unset($_SESSION["login"]);
   
   $_POST['login'] = str_replace("'", "", $_POST['login']);
   $_POST['login'] = str_replace('"', "", $_POST['login']);
   
   $_POST['senha'] = str_replace("'", "", $_POST['senha']);
   $_POST['senha'] = str_replace('"', "", $_POST['senha']);
   
   $userDAO = new UserDAO();
   $logado  = $userDAO->autenticarUsuario($_POST['login'], $_POST['senha']);
   
   if ($logado)
   {
      $_SESSION["login"] = $_POST['login'];
      
      header('Location: Game.php');
   }
   else
   {
      echo "<script>";
      echo "  alert('Login ou Senha inválido!');";
      echo "</script>";
   }
}

?>
<html>
   <script>
   function validaLogin()
   {
      var sLogin = document.getElementById('login').value;
      var sSenha = document.getElementById('senha').value;
      
      if(sLogin != '' && sSenha != '')
      {
         document.getElementById('frm_login').submit();
      }
      else
      {
         alert('Login e Senha são obrigatórios!')
      }
   }
   </script>
   <body>
      <form id="frm_login"  method="post" action="Login.php">
         <br>
         <br>
         <center>
            <table border="0" style="width: 80%">
               <tr>
                  <td style="text-align: right">
                     Login:&nbsp;
                  </td>
                  <td>
                     <input name="login" id="login" type="text" style="width: 100%">
                  </td>
               </tr>
               <tr>
                  <td style="text-align: right">
                     Senha:&nbsp;
                  </td>
                  <td>
                     <input name="senha" id="senha" type="password" style="width: 100%">
                  </td>
               </tr>
               <tr>
                  <td colspan="2">
                     <button type="button" style="width: 100%" onclick="validaLogin();">Entrar</button>
                  </td>
               </tr>
            </table>
         </center>
      </form>
   </body>
</html>
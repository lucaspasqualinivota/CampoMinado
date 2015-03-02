<?php

require_once(dirname(__FILE__)."/Server.php");

/**
 * Description of UserDAO
 *
 * @author Lucas
 */
class UserDAO
{
   private $usuario;
   
   public function __construct()
   {
      $server        = new Server();
      $this->usuario = $server->getUsuario();
   }
   
   public function autenticarUsuario($usuario, $senha)
   {
      if (isset($this->usuario[$usuario]) && $this->usuario[$usuario] == $senha)
      {
         return true;
      }
      
      return false;
   }
}
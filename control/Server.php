<?php

/**
 * Description of Server
 *
 * @author Lucas
 */
class Server
{
   private $ip;
   private $porta;
   private $usuario = array("lucas"=>"lucas", "leo"=>"leo");
   
   function Server()
   {
      $this->ip    = "10.0.0.1";
      $this->porta = "9000";
   }
   
   function getIp()
   {
      return $this->ip;
   }
   
   function getPorta()
   {
      return $this->porta;
   }
   
   function getUsuario()
   {
      return $this->usuario;
   }
}
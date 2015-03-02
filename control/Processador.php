<?php

require_once(dirname(__FILE__)."/../model/WebSocket.php");

/**
 * Description of Prcessador
 *
 * @author Lucas
 */
class Processador extends WebSocket
{
   /**
    * CADA REQUISICAO REQUISITADA AO SERVIDOR
    */
   protected function process($user, $message)
   {
      foreach ($this->users as $user)
      {
         $this->send($user, $message);
      }
      
      //LOG SERVIDOR REQUISICOES
      echo "\nRequested resource : " . $user->requestedResource;
   }

   /**
    * CONEXAO ESTABILIZADA COM O SERVIDOR
   */
   protected function connected($user)
   {
      $welcome_message = 'Bem vindo a arena mortal.';
      $this->send($user, $welcome_message);
   }

   /**
    * CONEXAO FECHADA COM O SERVIDOR
    */
   protected function closed($user)
   {
      echo "Usuario desconectado: " . $user;
   }
}
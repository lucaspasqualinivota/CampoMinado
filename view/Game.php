<?php
ini_set("track_errors", 1);
header('Content-Type: text/html; charset=utf-8');

if (!isset($_SESSION))
{
   session_start();
}

if (!isset($_SESSION["login"]))
{
   die("Fuck Off!");
}

require_once(dirname(__FILE__)."/../control/Server.php");

$server = new Server();
?>

<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
   <head>
      <title>Minefield - Panzer</title>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <link rel="stylesheet" type="text/css" href="estilo/font.css">
      <style>	 
         #field {
            position: absolute;
            height: 482px;
            width: 725px;
            border: none;
            padding: 3px;
            background: url('sprites/fundo.png') repeat;
            top: 110px;
            left: 310px;
         }

         .position {
            width: 75px;
            height: 75px;
            display: inline-block;
            border: 1px solid #000;
            z-index: 2;
         }

         .navbar {
            width: 100%;
            height: 50px;
            background: -moz-linear-gradient(top, #006e2e 0%, #006e2e 100%);
            background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#006e2e), color-stop(100%,#006e2e));
            background: -webkit-linear-gradient(top, #006e2e 0%,#006e2e 100%); 
            background: -o-linear-gradient(top, #006e2e 0%,#006e2e 100%);
            margin: 0;
            top: 0px;
            left: 0px;
            position: absolute;
            color: #fff;
            font-weight: bold;
            font-family: Roboto;
         }

         .basefield {
            position: absolute;
            height: 16px;
            width: 100%;
            border: none;
            background: url('sprites/basefield.png') repeat-x;
            top: 598px;
            left: 0px;
         }

         .topofield {
            position: absolute;
            height: 13px;
            width: 100%;
            border: none;
            background: url('sprites/topofield.png') repeat-x;
            top: 100px;
            left: 0px;
         } 

         .base_one {
            position: relative;
            top: -30px;
            left: -5px;
            height: 100%;
            width: 100%;
            border: none;
            background: url('sprites/base.png') left no-repeat;	  
            z-index: 11;
         }

         .base_two {
            position: relative;
            top: -30px;
            right: 0px;
            height: 100%;
            width: 100%;
            border: none;
            background: url('sprites/base2.png') right no-repeat;
            z-index: 11;
         }

         .base_player_one {
            height: 490px;
            width: 330px;
            border: none;
            position: absolute;
            top: 110px;
            left: 0px;
            background: url('sprites/fundo.png') repeat;
         }

         .base_player_two {
            height: 490px;
            width: 320px;
            border: none;
            position: absolute;
            top: 110px;
            left: 1040px;
            background: url('sprites/fundo.png') repeat;
         }

         #player_one_panzer_one {
            width: 76px;
            height: 76px;	    
            position: absolute;
            left: 230px;
            z-index: 10;
            background: url('sprites/panzer1.png') no-repeat;
         }

         #player_two_panzer_one {
            width: 76px;
            height: 76px;	    
            position: absolute;
            left: 1045px;
            z-index: 10;
            background: url('sprites/panzer12.png') no-repeat;
         }

         .panzer {
            background: url('sprites/panzer_.png') no-repeat;
            width: 100%;
            height: 100%;
         }

         .panzer2 {
            background: url('sprites/panzer_2.png') no-repeat;
            width: 100%;
            height: 100%;
         }
      </style>
      <script src="script/jquery-2.1.3.min.js"></script>
      <script>
         var socket;
         var $minefieldPanzer;
         
         function init()
         {
            var host = "ws://<?=$server->getIp().":".$server->getPorta()?>/CampoMinado/StartWebSocket.php"; // SET THIS TO YOUR SERVER

            try
            {
               socket = new WebSocket(host);

               socket.onopen = function (msg)
               {

               };

               //Message received from websocket server
               socket.onmessage = function (msg)
               {
                  if (msg.data.indexOf('#RUN') != -1)
                  {
                     dados = msg.data.split('RUN');
                     eval(dados[1]);
                  }
                  else
                  {
                     log(msg.data);
                  }
               };

               //Connection closed
               socket.onclose = function (msg)
               {
                  log("Desconectado!");
               };

               socket.onerror = function ()
               {
                  log("Server error!");
               }
            }

            catch (ex)
            {
               log('Some exception : ' + ex);
            }

            $("msg").focus();
         }

         function send()
         {
            var txt, msg;
            txt = $("textarea#msg");
            msg = txt.val();

            if (!msg)
            {
               alert("Message can not be empty");
               return;
            }

            txt.value = "";
            txt.focus();

            try
            {
               socket.send(' <?= $_SESSION["login"] . ": " ?> ' + msg);
               ($('textarea#msg').val(''));
            }
            catch (ex)
            {
               log(ex);
            }
         }

         function log(msg)
         {
            if($('#log').html() != '')
            {
               $('#log').append('<br />');
            }
            $('#log').append('<span>'+msg+'<span>');
            $('#log').scrollTop($('#log').height());
         }

         function command(command)
         {
            socket.send('#RUN' + command);
         }

         function quit()
         {
            if (socket != null)
            {
               log("Goodbye!");
               socket.close();
               socket = null;
            }
         }
         
         function iniciar()
         {
            MinefieldPanzer = function ()
            {
               this.positionLeft;
               this.positionTop;
               this.jogadorVez = 1;

               this.setMineOnField = function ()
               {
                  var linha = ["e", "f", "a", "d", "e", "d", "a", "b", "c", "b", "f", "c"];
                  var coluna = ["4", "2", "5", "1", "7", "6", "4", "9", "8", "5", "4", "6"];

                  var minas = new Array();
                  while (minas.length < 20)
                  {
                     var pos1 = Math.floor((Math.random() * 12));
                     var pos2 = Math.floor((Math.random() * 12));

                     var local = linha[pos1] + "" + coluna[pos2];
                     if (!this.existsInArray(minas, local) && local !== "b4" && local !== "e7")
                     {
                        minas[minas.length] = local;
                     }
                  }

                  for (var i in minas)
                  {
                     $("#" + minas[i]).attr("mina", "true");
                  }
               };

               this.existsInArray = function (ateste, procurado)
               {
                  var retorno = false;
                  for (var i = 0; i < ateste.length; i++)
                  {
                     if (procurado == ateste[i])
                     {
                        retorno = true;
                        break;
                     }
                  }
                  return retorno;
               };

               this.validaMover = function (id)
               {
                  var result = false;
                  if (id !== "b4" && id !== "e7")
                  {
                     var top = 0;
                     var left = 0;

                     if (this.getJogadorVez() === 1)
                     {
                        var topPanzer = $("#player_one_panzer_one").offset().top;
                        var leftPanzer = $("#player_one_panzer_one").offset().left;
                     }
                     else
                     {
                        var topPanzer = $("#player_two_panzer_one").offset().top;
                        var leftPanzer = $("#player_two_panzer_one").offset().left;
                     }

                     if (topPanzer > this.positionTop)
                     {
                        top = topPanzer - this.positionTop;
                     }
                     else if (topPanzer < this.positionTop)
                     {
                        top = this.positionTop - topPanzer;
                     }

                     if (leftPanzer > this.positionLeft)
                     {
                        left = leftPanzer - this.positionLeft;
                     }
                     else if (leftPanzer < this.positionLeft)
                     {
                        left = this.positionLeft - leftPanzer;
                     }

                     if ((top >= 0 && top < 150) && (left >= 0 && left < 150))
                     {
                        result = true;
                     }
                  }
                  return result;
               };

               this.moverPanzer = function (id_field)
               {
                  var mover = $("#" + id_field).attr("mover");
                  if (mover === "true")
                  {
                     if (this.getJogadorVez() === 1)
                     {
                        var p = new Panzer("player_one_panzer_one");
                     }
                     else
                     {
                        var p = new Panzer("player_two_panzer_one");
                     }
                     if (p.getSide() == 1)
                     {
                        //p.setTurn(90);
                        p.setTop(this.positionTop);
                        p.setLeft(this.positionLeft);
                     }

                     if ($("#" + id_field).attr("mina") == "true")
                     {
                        if ($minefieldPanzer.getJogadorVez() == 1)
                        {
                           setTimeout('explode(1)', 1000);
                        }
                        else
                        {
                           setTimeout('explode(2)', 1000);
                        }
                     }

                     if (this.getJogadorVez() === 1)
                     {
                        this.setJogadorVez(2);
                     }
                     else
                     {
                        this.setJogadorVez(1);
                     }

                     audioPanzer();
                  }
               };

               this.getJogadorVez = function ()
               {
                  return this.jogadorVez;
               };

               this.setJogadorVez = function (id_player)
               {
                  this.jogadorVez = id_player;
               };

               this.setPositionLeft = function (left)
               {
                  this.positionLeft = left;
               };

               this.setPositionTop = function (top)
               {
                  this.positionTop = top;
               };
            };

            Panzer = function (id)
            {
               this.id = id;
               this.panzer = $("#" + id);
               this.side = 1;

               this.setTop = function (top)
               {
                  this.panzer.css({"top": top, "transition": "1s linear"});
               };

               this.setLeft = function (left)
               {
                  this.panzer.css({"left": left, "transition": "1s linear"});
               };

               this.setTurn = function (deg)
               {
                  this.panzer.css({"transform": "rotate(" + deg + "deg)", "transition": "1s linear"});
               };

               this.setSide = function (side)
               {
                  this.side = side;
               };

               this.getSide = function ()
               {
                  return this.side;
               };
            };

            PlayerOne = function (id, name)
            {
               this.id = id;
               this.name = name;
               this.points = 0;
            };

            PlayerTwo = function (id, name)
            {
               this.id = id;
               this.name = name;
               this.points = 0;
            };

            $minefieldPanzer = new MinefieldPanzer();
            $minefieldPanzer.setMineOnField();

            $(".position").mouseover(function () {
               $minefieldPanzer.setPositionLeft($("#" + this.id).offset().left);
               $minefieldPanzer.setPositionTop($("#" + this.id).offset().top);
               if ($minefieldPanzer.validaMover(this.id))
               {
                  var color = ($minefieldPanzer.getJogadorVez() === 1) ? "blue" : "red";
                  $("#" + this.id).css({"opacity": "0.5", "background": color});
                  $("#" + this.id).attr("mover", "true");
               }
            });

            $(".position").mouseout(function () {
               if (this.id !== "b4" && this.id !== "e7")
               {
                  $("#" + this.id).css({"opacity": "1", "background": "none"});
               }
               $("#" + this.id).removeAttr("mover");
            });

            explode = function (player)
            {
               if (player == 1)
               {
                  $(".panzer").css({"background": "none"});
                  $("#player_one_panzer_one").css({"background": "url('sprites/explosao.gif')", "height": "75px"});
                  $("#panzer1").css({"display": "none"});
                  setTimeout('$("#player_one_panzer_one").css({"display": "none"});', 3000);
               }
               else
               {
                  $(".panzer2").css({"background": "none"});
                  $("#player_two_panzer_one").css({"background": "url('sprites/explosao2.gif')", "height": "75px"});
                  $("#panzer2").css({"display": "none"});
                  setTimeout('$("#player_two_panzer_one").css({"display": "none"});', 3000);
               }
               setTimeout("audioExplodir()", 200);


            };

            $(".position").click(function ()
            {
               var sComando = "$minefieldPanzer.setPositionLeft(" + $('#' + this.id).offset().left + ");";
               sComando    += "$minefieldPanzer.setPositionTop($('#' + '"+this.id+"').offset().top);";
               sComando    += "$minefieldPanzer.moverPanzer('"+this.id+"');";

               command(sComando);
            });

            audioExplodir = function ()
            {
               var audio = document.getElementById("explosao");
               audio.play();
            };

            audioPanzer = function ()
            {
               var audio = document.getElementById("audio_tank");
               audio.play();
            };
         }
         
         $(document).ready(function ()
         {
            init();
            
            $("#player_one_panzer_one").css({"top": $("#c1").offset().top});
            $("#player_two_panzer_one").css({"top": $("#c1").offset().top});
            
            $("#b4").css({"background": "url('sprites/pedra.png') no-repeat"});
            $("#e7").css({"background": "url('sprites/stone.png') no-repeat"});
         });
      </script>
   </head>
   <body style="background: #000;">
      <nav class="navbar">
         <div style="margin: 13px; cursor: pointer" onclick="iniciar()">
            Iniciar
         </div>
      </nav>

      <audio id="audio_tank" src="audio/tank.mp3" preload="auto"></audio>
      <audio id="explosao" src="audio/explosao.mp3" preload="auto"></audio>

      <div id="player_one_panzer_one">
         <div class="panzer" id="panzer1"></div>
      </div>

      <div id="player_two_panzer_one">
         <div class="panzer2" id="panzer2"></div>
      </div>

      <div class="topofield"></div>

      <div class="base_player_one">
         <div class="base_one"></div>	    
      </div>

      <div id="field">
         <div class="position" mina="false" id="a1"></div>
         <div class="position" mina="false" id="a2"></div>
         <div class="position" mina="false" id="a3"></div>
         <div class="position" mina="false" id="a4"></div>
         <div class="position" mina="false" id="a5"></div>
         <div class="position" mina="false" id="a6"></div>
         <div class="position" mina="false" id="a7"></div>
         <div class="position" mina="false" id="a8"></div>
         <div class="position" mina="false" id="a9"></div>

         <div class="position" mina="false" id="b1"></div>
         <div class="position" mina="false" id="b2"></div>
         <div class="position" mina="false" id="b3"></div>
         <div class="position" mina="false" id="b4"></div>
         <div class="position" mina="false" id="b5"></div>
         <div class="position" mina="false" id="b6"></div>
         <div class="position" mina="false" id="b7"></div>
         <div class="position" mina="false" id="b8"></div>
         <div class="position" mina="false" id="b9"></div>

         <div class="position" mina="false" id="c1"></div>
         <div class="position" mina="false" id="c2"></div>
         <div class="position" mina="false" id="c3"></div>
         <div class="position" mina="false" id="c4"></div>
         <div class="position" mina="false" id="c5"></div>
         <div class="position" mina="false" id="c6"></div>
         <div class="position" mina="false" id="c7"></div>
         <div class="position" mina="false" id="c8"></div>
         <div class="position" mina="false" id="c9"></div>

         <div class="position" mina="false" id="d1"></div>
         <div class="position" mina="false" id="d2"></div>
         <div class="position" mina="false" id="d3"></div>
         <div class="position" mina="false" id="d4"></div>
         <div class="position" mina="false" id="d5"></div>
         <div class="position" mina="false" id="d6"></div>
         <div class="position" mina="false" id="d7"></div>
         <div class="position" mina="false" id="d8"></div>
         <div class="position" mina="false" id="d9"></div>

         <div class="position" mina="false" id="e1"></div>
         <div class="position" mina="false" id="e2"></div>
         <div class="position" mina="false" id="e3"></div>
         <div class="position" mina="false" id="e4"></div>
         <div class="position" mina="false" id="e5"></div>
         <div class="position" mina="false" id="e6"></div>
         <div class="position" mina="false" id="e7"></div>
         <div class="position" mina="false" id="e8"></div>
         <div class="position" mina="false" id="e9"></div>

         <div class="position" mina="false" id="f1"></div>
         <div class="position" mina="false" id="f2"></div>
         <div class="position" mina="false" id="f3"></div>
         <div class="position" mina="false" id="f4"></div>
         <div class="position" mina="false" id="f5"></div>
         <div class="position" mina="false" id="f6"></div>
         <div class="position" mina="false" id="f7"></div>
         <div class="position" mina="false" id="f8"></div>
         <div class="position" mina="false" id="f9"></div>
      </div>

      <div class="base_player_two">
         <div class="base_two"></div>	    
      </div>

      <div class="basefield"></div>
      <div style="position: absolute; bottom: 20px; left: 4px; height: 30px; width: 500px;">
         <textarea id="msg" style="width: 87%; height: 100%; resize: none; margin-top: 5px;"></textarea>
         <button onclick="send()" id="enviar" style="height: 100%; float: right; margin-top: 8px;">Enviar</button>
      </div>
      <div id="log" style="overflow:auto; position: absolute; bottom: 0px; right: 0px; height: 130px; width: 280px; background-color: white"></div>
   </body>
</html>
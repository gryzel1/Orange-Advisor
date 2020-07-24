<?php require_once('include/db.php');
$db -> set_charset("utf8");

if (!$_SESSION["cuid"]) {
  header('Location: login.php');
}

if (!$_SESSION["cuid"]=="test0000") {
  header('Location: index.php');
}

?>

<!DOCTYPE html>
<html lang="fr">

  <!-- head -->
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="initial-scale=1,width=device-width">
    <title> Orange Advisor - Liste</title>

    <!-- CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.7.5/css/bulma.min.css">
    <link rel="stylesheet" href="css/main.css">
    <!-- CSS -->

    <!-- fonts -->
    <link href="https://fonts.googleapis.com/css?family=Montserrat:700&display=swap" rel="stylesheet">
    <script defer src="https://use.fontawesome.com/releases/v5.3.1/js/all.js"></script>
    <!-- fonts -->

    <!-- icon -->
    <link rel="shortcut icon" href="img/icon.png">
    <!-- icon -->

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.6.0/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.6.0/dist/leaflet.js"></script>

    <!-- JS -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
    <script src="js/main.js"></script>
    <!-- JS -->
  </head>

  <body>
    <div class="header" style="height:6rem">
      <div class="block container margin-auto">
        <div class="is-header">
          <div class="columns">
            <div class="column">
              <p class="advisor"><img src="img/logo.png" alt="Orange" style="width:50px;margin-top:15px"> Advisor </a><a href="index.php" class="is-hidden-desktop" style="color:white;font-size:20px"><i class="fas fa-home"></i></a></p>
            </div>
            <div class="column button-column">
              <br>
              <a id="addButton" class="orange-button is-hidden-touch" href="index.php"><i class="fas fa-map-marked-alt"></i>&nbsp;&nbsp;Retour à la carte</a>
              <br><br>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="block container margin-auto">
      <?php
        $queryResto = $db->query("SELECT * FROM resto");
        while($rowResto = $queryResto->fetch_assoc()){
          $id = $rowResto['id'];
          $nom = $rowResto['nom'];
          $commune = $rowResto['commune'];
          $x = $rowResto['x'];
          $y = $rowResto['y'];
          $plat = $rowResto['plat'];
          $tel = $rowResto['tel'];
          $nom = str_replace("\'","'",$nom);
          if ($commune=="") {
            $commune = "Non défini";
          }
          if ($plat=="") {
            $plat = "Non défini";
          }else {
            $plat .= "€";
          }
          if($tel){
            $telStr=' &nbsp;&nbsp;/&nbsp;&nbsp; <span class="orange-color"><i class="fa fa-phone"></i></span>&nbsp;&nbsp;'.$tel;
          }else{
            $telStr=' &nbsp;&nbsp;/&nbsp;&nbsp; <span class="orange-color"><i class="fa fa-phone"></i></span>&nbsp;&nbsp; Non défini';
          }
          echo '<div class="box is-hidden-touch" style="width:75%;margin:auto;margin-top:15px;">
            <article class="media">
              <div class="media-content">
                <div class="content">
                  <p>
                    <span style="float:right"><a href="delResto.php/?id='.$id.'" class="button is-success is-modify"><i class="fas fa-trash-alt"></i></a></span>
                    <span style="float:right;margin-right:10px;"><a id="select'.$id.'" class="button is-success is-modify"><i class="fas fa-pen"></i></a></span>
                    <strong>'.$nom.'</strong>
                    <br>
                    <span class="orange-color"><i class="fas fa-city"></i></span>&nbsp;&nbsp;'.$commune.' &nbsp;&nbsp;/ &nbsp;&nbsp;<span class="orange-color"><i class="fas fa-map-pin"></i></span>&nbsp;&nbsp;<strong>Longitude :</strong> '.$x.' - <strong>Latitude :</strong> '.$y.' &nbsp;&nbsp;/&nbsp;&nbsp; <span class="orange-color"><i class="fas fa-money-bill-wave"></i></span>&nbsp;&nbsp;'.$plat.$telStr.'
                  </p>
                </div>
              </div>
            </article>
          </div>';

          $telStr=substr($telStr,26);

          echo '<div class="box is-hidden-desktop" style="width:90%;margin:auto;margin-top:15px;">
            <article class="media">
              <div class="media-content">
                <div class="content">
                  <p>
                    <span style="float:right"><a href="delResto.php/?id='.$id.'" class="button is-success is-modify"><i class="fas fa-trash-alt"></i></a></span>
                    <span style="float:right;margin-right:10px;"><a id="select'.$id.'Mobile" class="button is-success is-modify"><i class="fas fa-pen"></i></a></span>
                    <strong>'.$nom.'</strong>
                    <br><br>
                    <span class="orange-color"><i class="fas fa-city"></i></span>&nbsp;&nbsp;'.$commune.'<br><span class="orange-color"><i class="fas fa-map-pin"></i></span>&nbsp;&nbsp;<strong>Longitude :</strong> '.$x.' - <strong>Latitude :</strong> '.$y.'<br><span class="orange-color"><i class="fas fa-money-bill-wave"></i></span>&nbsp;&nbsp;'.$plat.'<br>'.$telStr.'
                  </p>
                </div>
              </div>
            </article>
          </div>';
          if ($commune=="Non défini") {
            $commune = "";
          }
          if ($plat=="Non défini") {
            $plat = "";
          }
          $plat = str_replace("€","",$plat);
          echo '<div id="restoDesc'.$id.'" class="modal">
               <!-- Modal content -->
               <div class="modal-content">
                 <div class="addResto-header">
                 <span style="margin-right:15px;color:white" id="close'.$id.'" class="close">&times;</span>
                  <p><i class="fas fa-utensils"></i>&nbsp;&nbsp;'.$nom.' - '.$commune.'</p>
                 </div>
                 <form id="form'.$id.'" method="post" onsubmit="return false" action="modResto.php">
                   <div class="columns" style="margin:auto">
                     <div class="column">
                       <div class="field">
                        <label style="margin-top:15px" class="label">Nom*</label>
                        <div class="control">
                          <input id="nom'.$id.'" class="input" type="text" placeholder="Nom du restaurant" tabindex="1" name="nom" value="'.$nom.'">
                        </div>
                      </div>
                      <div class="field">
                       <label class="label">Plat du jour</label>
                       <div class="control">
                         <input id="plat'.$id.'" class="input" type="text" placeholder="Prix du plat du jour" tabindex="3" name="plat" value="'.$plat.'">
                       </div>
                     </div>
                    <div class="field">
                     <label class="label">Longitude*</label>
                     <div class="control">
                       <input id="x'.$id.'" class="input" type="text" placeholder="x" tabindex="5" name="x" value="'.$x.'">
                     </div>
                   </div>
                     </div>
                     <div class="column">
                       <div class="field">
                        <label style="margin-top:15px" class="label">Commune</label>
                        <div class="control">
                          <input id="commune'.$id.'" class="input" type="text" placeholder="Commune du restaurant" tabindex="2" name="commune" value="'.$commune.'">
                        </div>
                      </div>
                      <div class="field">
                       <label class="label">Téléphone</label>
                       <div class="control">
                         <input id="tel'.$id.'" class="input" type="text" placeholder="** ** ** ** **" tabindex="4" name="tel" value="'.$tel.'">
                       </div>
                     </div>
                     <div class="field">
                      <label class="label">Latitude*</label>
                      <div class="control">
                        <input id="y'.$id.'" class="input" type="text" placeholder="y" tabindex="6" name="y" value="'.$y.'">
                      </div>
                    </div>
                   </div>
                 </div>
                 <div class="field is-grouped" style="display:block;margin:auto;margin-top:20px">
                  <div class="control">
                  <input type="hidden" name="id" value="'.$id.'">
                    <button id="submit-button'.$id.'" type="submit" name="submit" class="button is-link is-orange"><i class="fas fa-pen"></i>&nbsp;&nbsp;Modifier le restaurant</button>
                  </div>
                 </div>
                 </form>
               </div>
              </div>';
          echo '<script>  window.addEventListener("load", function () {// Get the modal
            var modal'.$id.' = document.getElementById("restoDesc'.$id.'");

            // Get the button that opens the modal
            var btn'.$id.' = document.getElementById("select'.$id.'");
            var btn'.$id.'Mobile = document.getElementById("select'.$id.'Mobile");

            // Get the <span> element that closes the modal
            var span'.$id.' = document.getElementById("close'.$id.'");

            // When the user clicks on the button, open the modal
            btn'.$id.'.onclick = function() {
              modal'.$id.'.style.display = "block";
            }
            btn'.$id.'Mobile.onclick = function() {
              modal'.$id.'.style.display = "block";
            }

            // When the user clicks on <span> (x), close the modal
            span'.$id.'.onclick = function() {
              modal'.$id.'.style.display = "none";
            }
          })</script>';
          echo '<script>var submit'.$id.' = document.getElementById("submit-button'.$id.'");
            var nom'.$id.' = document.getElementById("nom'.$id.'");
            var commune'.$id.' = document.getElementById("commune'.$id.'");
            var plat'.$id.' = document.getElementById("plat'.$id.'");
            var tel'.$id.' = document.getElementById("tel'.$id.'");
            var x'.$id.' = document.getElementById("x'.$id.'");
            var y'.$id.' = document.getElementById("y'.$id.'");
            var form'.$id.' = document.getElementById("form'.$id.'");

            submit'.$id.'.onclick = function() {
              var check'.$id.'=true;
              if (nom'.$id.'.value=="") {
                nom'.$id.'.className="input is-danger";
                check'.$id.'=false;
              }else{
                nom'.$id.'.className="input is-success";
              }

              if (!parseFloat(x'.$id.'.value)) {
                x'.$id.'.className="input is-danger";
                check'.$id.'=false;
              }else{
                x'.$id.'.className="input is-success";
              }

              if (!parseFloat(y'.$id.'.value)) {
                y'.$id.'.className="input is-danger";
                check'.$id.'=false;
              }else{
                y'.$id.'.className="input is-success";
              }

              if (commune'.$id.'.value=="") {
                commune'.$id.'.className="input is-warning";
              }else{
                commune'.$id.'.className="input is-success";
              }

              if (plat'.$id.'.value=="") {
                plat'.$id.'.className="input is-warning";
              }else if (parseFloat(plat'.$id.'.value)){
                plat'.$id.'.className="input is-success";
              }else{
                plat'.$id.'.className="input is-danger";
                check'.$id.'=false;
              }

              if(tel'.$id.'.value=="") {
                tel'.$id.'.className="input is-warning";
              }else if(parseFloat(tel'.$id.'.value)){
                tel'.$id.'WithoutSpace = tel'.$id.'.value.replace(/\s+/g, "");
                if(tel'.$id.'WithoutSpace.length==10){
                  tel'.$id.'.className="input is-success";
                }else{
                  tel'.$id.'.className="input is-danger";
                  check'.$id.'=false;
                }
              }else{
                tel'.$id.'.className="input is-danger";
                check'.$id.'=false;
              }

              if (check'.$id.'==true) {
                form'.$id.'.setAttribute("onSubmit", "");
                form'.$id.'.submit();
              }
            }</script>';
        }
        echo "<script>window.onclick = function(event) {";
        $queryResto = $db->query("SELECT * FROM resto");
        while($rowResto = $queryResto->fetch_assoc()){
          $id = $rowResto['id'];
          echo 'var modal'.$id.' = document.getElementById("restoDesc'.$id.'");
              if (event.target == modal'.$id.') {
                modal'.$id.'.style.display = "none";
              }';
        }
        echo "}</script>";
      ?>
    </div>
</div>

  </body>
</html>

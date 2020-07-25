<?php require_once('include/db.php');
$db -> set_charset("utf8");

if(isset($_POST['cuid'])){
  $cuid = strtolower($_POST['cuid']);
  $passwd = $_POST['passwd'];

  $queryLogin = $db->query("SELECT * FROM user WHERE cuid LIKE '".$cuid."';");
  $rowLogin = $queryLogin->fetch_assoc();
  $motdepasse = $rowLogin['motdepasse'];
  $prenom = $rowLogin['prenom'];
  $nom = $rowLogin['nom'];

  if ($passwd==$motdepasse) {
      $_SESSION["cuid"]=$cuid;
      $_SESSION["prenom"]=$prenom;
      $_SESSION["nom"]=$nom;
  }else {
    header('Location: login.php');
  }
}

if($_SESSION["cuid"]=="test0000"){
  $prenom = "Invité";
  $_SESSION["prenom"]=$prenom;
}

if (!$_SESSION["cuid"]) {
  header('Location: login.php');
}

?>

<!DOCTYPE html>
<html lang="fr" style="overflow-y: hidden;">

  <!-- head -->
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="initial-scale=1,width=device-width">
    <title> Orange Advisor - Accueil</title>

    <!-- CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.7.5/css/bulma.min.css">
    <link rel="stylesheet" href="http://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <link href="bulma-badge/dist/css/bulma-badge.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/main.css">
    <link href="https://use.fontawesome.com/releases/v5.0.1/css/all.css" rel="stylesheet">
    <link rel="stylesheet" href="css/leaflet.extra-markers.min.css">
    <link rel="stylesheet" href="css/MarkerCluster.css">
    <link rel="stylesheet" href="css/MarkerCluster.Default.css">
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
    <script src="js/leaflet.extra-markers.min.js"></script>
    <script src="js/leaflet.markercluster-src.js"></script>
    <script src="js/main.js"></script>
    <!-- JS -->
  </head>

  <body>
    <div class="header">
      <div class="block container margin-auto">
        <div class="is-header">
          <div class="columns">
            <div class="column">
              <p class="advisor"><img src="img/logo.png" alt="Orange" style="width:50px;margin-top:15px"> Advisor <?php if($_SESSION["cuid"]!="test0000")echo '<a id="addButtonMobile" class="is-hidden-desktop" style="color:white;font-size:20px;"><i class="fas fa-plus-square"></i></a><a href="modify.php" class="is-hidden-desktop" style="color:white;font-size:20px;margin-left:5px"><i class="fas fa-pen-square"></i></a>'; ?></p>
              <p class="prenom"><span class="bjr">Bonjour </span><?php echo $_SESSION["prenom"]; ?> <a href="disconnect.php" style="color:white"><i class="fas fa-sign-out-alt"></i></a></p>
            </div>
            <div class="column button-column">
              <br>
              <?php
              if($_SESSION["cuid"]!="test0000"){
                echo '<a id="addButton" class="orange-button is-hidden-touch" href="#"><i class="fas fa-plus"></i>&nbsp;&nbsp;Ajouter un restaurant</a>
                <br><br>
                <a class="orange-button is-hidden-touch" href="modify.php"><i class="fas fa-pen"></i>&nbsp;&nbsp;Modifier la liste</a>';
              }
               ?>
            </div>
          </div>

        </div>

      </div>
    </div>
    <div id="map"></div>
    <script src="js/map.js"></script>
    <?php
      echo "<script> var popups = [];
      var markersCluster = new L.MarkerClusterGroup({
        maxClusterRadius:40,
        iconCreateFunction: function(cluster) {
        return L.divIcon({
            html: cluster.getChildCount(),
            className: 'cluster',
            iconSize: null
        });
    }
      });
      map.addLayer(markersCluster);</script>";

      $queryResto = $db->query("SELECT * FROM resto");
      while($rowResto = $queryResto->fetch_assoc()){
        $nom = $rowResto['nom'];
        $queryMoy = $db->query('SELECT ROUND(AVG(note),2) FROM rating where nom like "'.$nom.'"');
        $rowMoy = $queryMoy->fetch_assoc();
        $moy = $rowMoy['ROUND(AVG(note),2)'];

        $nom = str_replace("'","\'",$nom);
        $x = $rowResto['x'];
        $y = $rowResto['y'];
        $plat = $rowResto['plat'];
        $tel = $rowResto['tel'];
        $id = $rowResto['id'];
        $commune = $rowResto['commune'];

        if ($plat=="") {$€="";}else {$€="€";}
        if ($moy!="" && $plat=="") {$sep="";}else{$sep=" - ";}
        if ($moy=="") {$etoile="";$sep="";}else {$etoile="&#9733";}
        $queryNum = $db->query('SELECT COUNT(*) FROM reservation where date like "'.$date.'" and id like '.$id);
        echo "<script>var popup".$id." = L.popup({
          closeButton:false,
          autoClose:false,
          closeOnEscapeKey:false,
          closeOnClick:false,
          minWidth:100,
          autoPan:false
        })  .setLatLng(L.latLng(".$x.",".$y."))";
        $rowNum = $queryNum->fetch_assoc();
        $num = $rowNum['COUNT(*)'];
        if ($num==1) {
          $inscrits=$num." inscrit";
        }else {
          $inscrits=$num." inscrits";
        }
        if ($num) {
          echo ".setContent('<a class=\"select".$id."\" style=\"display:inline-flex;text-align:center;color: #f16e00;\"><i class=\"fas fa-utensils\"></i>&nbsp;&nbsp;".$nom."<br>".$plat.$€.$sep.$moy.$etoile."</a><p data-badge=\"".$inscrits."\" class=\"has-badge-rounded has-badge-orange\"></p>')
              ;popups.push(popup".$id.");</script>";
        }else {
          echo ".setContent('<a class=\"select".$id."\" style=\"display:inline-flex;text-align:center;color: #f16e00;\"><i class=\"fas fa-utensils\"></i>&nbsp;&nbsp;".$nom."<br>".$plat.$€.$sep.$moy.$etoile."</a>')
              ;popups.push(popup".$id.");</script>";
        }
        echo "<script>var orangeMarker = L.ExtraMarkers.icon({
            icon: 'fa-utensils',
            markerColor: '#f16e00',
            svg: 'true',
            shape: 'circle',
            prefix: 'fas'
          });
          var marker".$id." = L.marker([".$x.", ".$y."], {icon: orangeMarker});
          marker".$id.".bindPopup(popup".$id.");
          markersCluster.addLayer(marker".$id.");</script>";
        $nom = str_replace("\'","'",$nom);
        echo '<div id="restoDesc'.$id.'" class="modal">
             <!-- Modal content -->
             <div class="modal-content" style="height:80%;">
               <div class="addResto-header">
               <span style="margin-right:15px;color:white" id="close'.$id.'" class="close">&times;</span>
                <p><i class="fas fa-utensils"></i>&nbsp;&nbsp;'.$nom.' - '.$commune.'</p>
               </div>
               <div class="box" style="width:100%;margin:auto;margin-top:15px;">
                 <article class="media">
                   <div class="media-content">
                     <div class="content">
                       <p style="text-align:center">
                         <strong>Qui mange ici ce midi ?</strong>
                         <br>
                          <table class=" table is-striped is-bordered">';
                          $date = date('d/m/Y');
                          $queryJoin = $db->query('SELECT * FROM reservation where date like "'.$date.'" and id = '.$id.'');
                          $count=0;
                          while($rowJoin = $queryJoin->fetch_assoc()){
                            $cuid = $rowJoin['cuid'];
                            $queryName = $db->query('SELECT * FROM user where cuid like "'.$cuid.'"');
                            $rowName = $queryName->fetch_assoc();
                            $prenom = $rowName['prenom'];
                            $nomR = $rowName['nom'];
                            if (!$count%2) {
                              echo '<tr><td style="text-align:center;width:50%">'.$prenom.' '.$nomR.'</td>';
                            }
                            else {
                              echo '<td style="text-align:center;width:50%">'.$prenom.' '.$nomR.'</td></tr>';
                            }
                            $count++;
                          }
                          if ($count%2) {
                            echo "</tr>";
                          }
                          echo '</table>';
                          echo '<div class="field is-grouped">
                           <div class="control" style="display:block;margin:auto">';
                          $queryPresent = $db->query('SELECT * FROM reservation where date like "'.$date.'" and id = '.$id.' and cuid like "'.$_SESSION["cuid"].'"');
                          $rowPresent = $queryPresent->fetch_assoc();
                          if(!$rowPresent&&$_SESSION["cuid"]!="test0000"){
                            echo '<a id="joinResto'.$id.'" href="joinResto.php/?id='.$id.'" style="padding-right:10px;padding-left:10px;" class="button is-link is-orange"><i class="fas fa-utensils"></i>&nbsp;&nbsp;Manger ici</a>';
                          }else if($_SESSION["cuid"]!="test0000"){
                            echo '<a id="leaveResto'.$id.'" href="leaveResto.php/?id='.$id.'" style="padding-right:10px;padding-left:10px;" class="button is-link is-orange"><i class="fas fa-times"></i>&nbsp;&nbsp;Ne plus manger ici</a>';
                          }
                          echo '</div>
                          </div>
                       </p>
                     </div>
                   </div>
                 </article>
               </div>
               <div class="box is-hidden-desktop" style="width:100%;margin:auto;margin-top:15px;">
                 <article class="media">
                   <div class="media-content">
                     <div class="content">
                       <p>
                         <strong>Coordonnées</strong>
                         <br>
                           <div class="field is-grouped">
                            <div class="control" style="display:block;margin:auto">
                              <a href="geo:'.$x.','.$y.'" style="padding-right:10px;padding-left:10px;" class="button is-link is-orange"><i class="fa fa-map-marker"></i>&nbsp;&nbsp;Se rendre sur place</a>';
                              if($tel)echo '<a href="tel:'.$tel.'" style="padding-right:10px;padding-left:10px;margin-top:10px" class="button is-link is-orange"><i class="fa fa-phone"></i>&nbsp;&nbsp;Réserver</a>';
                            echo '</div>
                           </div>
                       </p>
                     </div>
                   </div>
                 </article>
               </div>';
               if($_SESSION["cuid"]!="test0000"){
                 echo '<form id="form" method="post" onsubmit="<!--return false-->" action="addRating.php">
                 <div class="box" style="width:100%;margin:auto;margin-top:15px;">
                   <article class="media">
                     <div class="media-content">
                       <div class="content" style="text-align:center">
                         <p><h1 style="margin-top:0;">Donnez-nous votre avis.</h1>
                         <br>
                           <x-star-rating style="text-align:center;margin-top:0" class="star" value="0" number="5"></x-star-rating>
                           <br>
                           <input id="commentaire" class="input" type="text" placeholder="Commentaire" tabindex="1" name="commentaire">
                         </p>
                       </div>
                     </div>
                   </article>
                 </div>
                  <input class="star-input" type="hidden" name="stars"></input>
                  <input type="hidden" name="nom" value="'.$nom.'"></input>
                  <input type="submit" style="display: none;" />
                 </form>';
               }

         $queryRating = $db->query('SELECT * FROM rating where nom like "'.$nom.'"');
         while($rowRating = $queryRating->fetch_assoc()){
           $queryUsername = $db->query('SELECT * FROM user where cuid like "'.$rowRating['cuid'].'"');
           $rowUsername = $queryUsername->fetch_assoc();
           $cuidRating = $rowUsername['cuid'];
           $username = $rowUsername['prenom']." ".$rowUsername['nom'];
           $note = $rowRating['note'];
           $commentaire = $rowRating['commentaire'];
           echo '<div class="box" style="width:100%;margin:auto;margin-top:15px;">
             <article class="media">
               <div class="media-content">
                 <div class="content">
                   <p>
                     <strong>'.$username.'</strong><span class="orange-color">&nbsp;&nbsp;';
           for ($i = 1; $i <= $note; $i++) {
               echo '<i class="fas fa-star"></i>';
           }
           for ($i = 1; $i <= 5-$note; $i++) {
               echo '<i class="far fa-star"></i>';
           }
          echo '</span>
                     <br>
                     '.$commentaire;
                     if($cuidRating==$_SESSION['cuid']) echo '<span style="float:right"><a href="delRating.php/?cuid='.$cuidRating.'&nom='.$nom.'" class="button is-success is-modify"><i class="fas fa-trash-alt"></i></a></span>';
                   echo '</p>
                 </div>
               </div>
             </article>
           </div>';
         }
        echo '</div>
     </div>';
        echo '<script>  window.addEventListener("load", function () {// Get the modal
          var modal'.$id.' = document.getElementById("restoDesc'.$id.'");

          // Get the button that opens the modal
          var btn'.$id.' = document.getElementById("select'.$id.'");

          // Get the <span> element that closes the modal
          var span'.$id.' = document.getElementById("close'.$id.'");

          // When the user clicks on the button, open the modal
          // btn'.$id.'.onclick = function() {
          //   modal'.$id.'.style.display = "block";
          // }

          jQuery("body").on("click","a.select'.$id.'", function(e){
            e.preventDefault();
            modal'.$id.'.style.display = "block";
          });

          // When the user clicks on <span> (x), close the modal
          span'.$id.'.onclick = function() {
            modal'.$id.'.style.display = "none";
          }
        })</script>';
      }
      echo "<script>window.onclick = function(event) {";
      echo 'var modal = document.getElementById("addResto");
          if (event.target == modal) {
            modal.style.display = "none";
          }';

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

    <div id="addResto" class="modal">

     <!-- Modal content -->
     <div class="modal-content">
       <div class="addResto-header">
       <span style="margin-right:15px;color:white" id="close" class="close">&times;</span>
        <p><i class="fas fa-plus"></i>&nbsp;&nbsp;Ajouter un restaurant</p>
       </div>
       <form id="form-resto" method="post" onsubmit="return false" action="addResto.php">
         <div class="columns" style="margin:auto">
           <div class="column">
             <div class="field">
              <label style="margin-top:15px" class="label">Nom*</label>
              <div class="control">
                <input id="nom" class="input" type="text" placeholder="Nom du restaurant" tabindex="1" name="nom">
              </div>
            </div>
            <div class="field">
             <label class="label">Plat du jour</label>
             <div class="control">
               <input id="plat" class="input" type="text" placeholder="Prix du plat du jour" tabindex="3" name="plat">
             </div>
           </div>
           <div class="field">
            <label class="label">Longitude*</label>
            <div class="control">
              <input id="x" class="input" type="text" placeholder="x" tabindex="5" name="x">
            </div>
          </div>
           </div>
           <div class="column">
             <div class="field">
              <label style="margin-top:15px" class="label">Commune</label>
              <div class="control">
                <input id="commune" class="input" type="text" placeholder="Commune du restaurant" tabindex="2" name="commune">
              </div>
            </div>
            <div class="field">
             <label class="label">Téléphone</label>
             <div class="control">
               <input id="tel" class="input" type="text" placeholder="** ** ** ** **" tabindex="4" name="tel">
             </div>
           </div>
           <div class="field">
            <label class="label">Latitude*</label>
            <div class="control">
              <input id="y" class="input" type="text" placeholder="y" tabindex="6" name="y">
            </div>
          </div>
         </div>
       </div>
       <div class="field is-grouped" style="display:block;margin:auto;margin-top:20px">
        <div class="control">
          <button id="submit-button" type="submit" name="submit" class="button is-link is-orange"><i class="fas fa-plus"></i>&nbsp;&nbsp;Ajouter le restaurant</button>
        </div>
       </div>
       </form>
     </div>

    </div>
</div>
  </body>
</html>

<!-- head -->
<head>
  <meta charset="utf-8">
  <title> Orange Advisor </title>

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

<?php

require_once('include/db.php');

if (!$_SESSION["cuid"]) {
  header('Location: login.php');
}

if(isset($_POST['nom'])){
  $nom = $_POST['nom'];
  $commune = $_POST['commune'];
  $plat = $_POST['plat'];
  $x = $_POST['x'];
  $y = $_POST['y'];
  $id = $_POST['id'];
}else {
  header('Location: modify.php');
}

if ($commune=="") {
  $commune=null;
}

if ($plat=="") {
  $plat=null;
}

$plat = str_replace (",", ".", $plat);
$x = str_replace (",", ".", $x);
$y = str_replace (",", ".", $y);

if (!$commune&&!$plat) {
  $db->query('update resto set nom="'.$nom.'",x="'.$x.'",y="'.$y.'",commune=NULL,plat=NULL where id like "'.$id.'"');
}elseif (!$plat) {
  $db->query('update resto set nom="'.$nom.'",x="'.$x.'",y="'.$y.'",commune="'.$commune.'",plat=NULL where id like "'.$id.'"');
}elseif (!$commune) {
  $db->query('update resto set nom="'.$nom.'",x="'.$x.'",y="'.$y.'",commune=NULL,plat="'.$plat.'" where id like "'.$id.'"');
}else {
  $db->query('update resto set nom="'.$nom.'",x="'.$x.'",y="'.$y.'",commune="'.$commune.'",plat="'.$plat.'" where id like "'.$id.'"');
}

header('Location: modify.php');

 ?>



<?php

  include("../BDD/Connexion.php");

  function printEvents($nb, $events, $ide) {
    $width = 80 / $nb;
    $height = 80 / $nb;

    for ($i = 0 ; $i != $nb ; $i++) {
      ?>
      <form class="eventForm" style="width: <?= $width; ?>vw; height: <?= $height; ?>vh" action="Home.php" method="post">
        <input type="hidden" name="idEvent" value="<?= $events[$i]->getID(); ?>">
        <input type="hidden" name="predEvent" value="<?= $events[$i]->getPred(); ?>">
        <div class="event" style="width: <?= $width; ?>vw; height: <?= $height; ?>vh">
          <textarea type="text" name="textEvent"><?= $events[$i]->getText(); ?></textarea>
        </div>
        <button type="submit" name="checkNextEvents">Voir</button>
        <button class="deleteEvent" type="submit" name="deleteEvent">Supprimer</button>
        <button class="updateEvent" type="submit" name="updateEvent">Modifier</button>
        <button class="insertEvent" type="submit" name="insertEvent">Développer</button>
      </form>
      <?php
    }

  }

  if ($_SESSION["startingScript"] == True) {
    $ids = $_SESSION['ids'];
    $sql = "SELECT * FROM event WHERE script = '$ids' AND pred IS NULL";
    $result = $con->query($sql);

    $res = [];
    while ($object = $result->fetch_object("Event")) {
      array_push($res, $object);
    }
    $_SESSION["eventsToPrint"] = $res;
    $_SESSION["startingScript"] = False;
    $_SESSION["pred"] = -1;
  }

  if(isset($_POST["checkNextEvents"])) {
    $ide = $_POST["idEvent"];
    $sql = "SELECT * FROM event WHERE pred = $ide";
    $result = $con->query($sql);
    if ($result->num_rows > 0) {
      $res = [];
      while ($object = $result->fetch_object("Event")) {
        array_push($res, $object);
      }
      $_SESSION["eventsToPrint"] = $res;
      $_SESSION["pred"] = $_POST["predEvent"];
    }

  }

  if(isset($_POST["deleteEvent"]) || isset($_POST["addEvent"]) || isset($_POST["updateEvent"])) {
    if(isset($_POST["deleteEvent"])) {
      deleteEvent($_POST["deleteEvent"]);
    }
    if(isset($_POST["addEvent"])) {
      addEvent($_POST["textEvent"], $_POST["predEvent"], $_POST["scriptEvent"]);
    }
    if(isset($_POST["updateEvent"])) {
      updateEvent($_POST["textEvent"], $_POST["idEvent"]);
    }

    $pred = $_POST["predEvent"];
    if ($pred == -1) {
      $ids = $_SESSION['ids'];
      $sql = "SELECT * FROM event WHERE pred IS NULL AND script = $ids";
    } else {
      $sql = "SELECT * FROM event WHERE pred = $pred";
    }

    $result = $con->query($sql);

    $res = [];
    while ($object = $result->fetch_object("Event")) {
      array_push($res, $object);
    }
    $_SESSION["eventsToPrint"] = $res;
    $_SESSION["pred"] = $_POST["predEvent"];

  }

  if(isset($_POST["insertEvent"])) {
    insertEvent("", $_POST["idEvent"], $_SESSION["ids"]);
    $ide = $_POST["idEvent"];
    $sql = "SELECT * FROM event WHERE pred = $ide";
    $result = $con->query($sql);
    $res = [];
    while ($object = $result->fetch_object("Event")) {
      array_push($res, $object);
    }
    $_SESSION["eventsToPrint"] = $res;
    $_SESSION["pred"] = $_POST["predEvent"];
  }

  if(isset($_POST["comeback"])) {
    $pred = $_SESSION["pred"];
    if ($pred == -1) {
      $ids = $_SESSION["ids"];
      $sql = "SELECT * FROM event WHERE pred IS NULL AND script = $ids";
    } else {
      $sql = "SELECT * FROM event WHERE pred = $pred";
    }
    $result = $con->query($sql);

    $res = [];
    while ($object = $result->fetch_object("Event")) {
      array_push($res, $object);
    }
    $_SESSION["eventsToPrint"] = $res;

    if ($pred == -1) {
      $_SESSION["pred"] = -1;
    } else {
      $pred = $_SESSION["eventsToPrint"][0]->getPred();
      $sql = "SELECT pred FROM event WHERE ide = $pred";
      $result = $con->query($sql);

      if (is_null($result->fetch_row()[0])) {
        $_SESSION["pred"] = -1;
      } else {
        $_SESSION["pred"] = $result->fetch_row()[0];
      }
    }
  }

?>

<div id="mode">

  <form class="scriptForm" action="Home.php" method="post">
    <button type="submit" name="comeback">Retour</button>
  </form>

  <form class="scriptForm" action="Home.php" method="post">
    <input type="hidden" name="predEvent" value="<?= $_SESSION["eventsToPrint"][0]->getPred(); ?>">
    <input type="hidden" name="scriptEvent" value="<?= $_SESSION["ids"]; ?>">
    <input type="text" name="textEvent">
    <button class="addEvent" type="submit" name="addEvent">Ajouter un event</button>
  </form>

  <form class="scriptForm" action="Home.php" method="post">
    <input type="text" name="scriptName" value="<?= $_SESSION["scriptName"]; ?>">
    <button type="submit" name="updateScript">Modifier</button>
  </form>

</div>

<div id="content">
  <?php
    printEvents(sizeof($_SESSION["eventsToPrint"]), $_SESSION["eventsToPrint"], $_SESSION["pred"]);
  ?>
</div>

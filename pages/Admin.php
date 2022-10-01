<?php
if (!array_key_exists("section", $_GET) || !array_key_exists("pageModif", $_GET)) {
  header("location:index.php?page=Admin&pageModif=Acceuil&section=1");
  die();
}

$finalUrl = "index.php?page=Admin&pageModif=Acceuil&section=1";
require("classes/GPXmanagement.php");
if (isset($_FILES['trace'])) {
  GPX::uploadGPX_updateDB($_FILES["trace"]);
  header("location:$finalUrl");
  die();
}

if (isset($_FILES['traces'])) {
  GPX::uploadGPX_updateDB_multiple();
  header("location:$finalUrl");
  die();
}

$section = $_GET["section"];
$pageModif = $_GET["pageModif"];
if (array_key_exists("contenu", $_POST)) {
  extract($_POST);
  $article = new Content(
    $pageModif,
    $section,
    $sous_section,
    $contenu
  );
  $article->update_db();
}
?>
<div class="pageContainer">
  <?php
  foreach ($page_list as $page) {
    if ($page["content"]) {
      if ($_GET["pageModif"] == $page["name"]) {
        echo "<div id=activeModif><div>";
      } else
        echo "<div><div>";
  ?>
      <a href="index.php?page=Admin&pageModif=<?= $page["name"] ?>&section=1"><?= $page["title"] ?></a>
</div>
</div>
<?php
    }
  }
?>
</div>

<section class="adminSection">
  <?php
  $select = $conn->prepare("SELECT COUNT(DISTINCT(section)) FROM content WHERE page=?");
  $select->execute(array($pageModif));
  $n_sec = $select->fetch()[0];

  $select = $conn->prepare("SELECT * FROM content WHERE page=? and section=?");
  $select->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Content');
  $select->execute(array($pageModif, $section));
  ?>


  <!-- Choix de la vue -->
  <div class="btn-group adminView" role="group" aria-label="Basic radio toggle button group">
    <input type="radio" class="btn-check" name="btnradio" id="btnradio1" onclick="changeView('AdminModification', 'AdminStructure')" checked>
    <label class="btn btn-outline-primary" for="btnradio1">Structure</label>

    <input type="radio" class="btn-check" name="btnradio" id="btnradio2" onclick="changeView('AdminStructure', 'AdminModification')">
    <label class="btn btn-outline-primary" for="btnradio2">Modification</label>
  </div>

  <!-- Vue Modif -->
  <div id="AdminModification">
    <div class="adminTitle">
      <div>
        <h2><b>Section <?= $section ?></b></h2>
      </div>
      <div>
        <button>Créer une sous-section</button>
        <form action="post"><input type="text">c'est pour créer une nouvelle sous-section</form>
      </div>
    </div>
    <?php

    /* bouton de choix de la section à modifier */
    if ($n_sec != 0) {
    ?>
      <footer>
        <div>Choix de section</div>
        <nav aria-label="About pagination">
          <ul class="pagination">
            <?php
            if ($section == 1)
              $sectionP = $section;
            if ($section == $n_sec)
              $sectionS = $section;
            if (!isset($sectionP))
              $sectionP = $section - 1;
            if (!isset($sectionS))
              $sectionS = $section + 1;

            echo <<<END
          <li class="page-item"><a class="page-link" href="index.php?page=Admin&pageModif=$pageModif&section=$sectionP">Précédent</a></li>
          END;
            for ($k = 1; $k <= $n_sec; $k++) {
              echo <<<END
            <li class="page-item"><a class="page-link" href="index.php?page=Admin&pageModif=$pageModif&section=$k">$k</a></li>
            END;
            }
            echo <<<END
          <li class="page-item"><a class="page-link" href="index.php?page=Admin&pageModif=$pageModif&section=$sectionS">Suivant</a></li>
          END;
            ?>
          </ul>
        </nav>
      </footer>
    <?php
    }

    /* Impression du contenu de la section de la page demandée */
    while ($article = $select->fetch()) {
    ?>
      <article>
        <form action="index.php?page=Admin&pageModif=<?= $pageModif ?>&section=<?= $section ?>" method="post">
          <p>
            <label for="contenu">
              <?= $article->description ?></label>
          </p>
          <textarea type="text" name="contenu" id="contenu"><?= $article->contenu ?></textarea>
          <input type=number name=sous_section value=<?= $article->sous_section ?> hidden>
          <br>
          <button type="submit">Modifier cette sous-section</button>
        </form>
      </article>
      <br>
    <?php
    }
    ?>
  </div>

  <!-- Vue Structure -->
  <div id="AdminStructure">
    Voici la structure
    <?php
    $select = $conn->query("SELECT * FROM content ORDER BY page, section, sous_section");
    $select->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Content');
    $page = null;
    $section = null;
    $firstP = true;
    $parité = "pair";
    echo "<ul id=myUL>";
    while ($sub_section = $select->fetch()) {
      $Npage = $sub_section->page;
      $Nsection = $sub_section->section;
      $desc = $sub_section->description;
      if ($Npage != $page) {
        $page = $Npage;
        $firstS = true;
        if ($parité == "pair")
          $parité = "impair";
        else
          $parité = "pair";
        if (!$firstP)
          echo "</ul></li></ul></div>";
        else
          $firstP = false;
        echo "<div class=$parité><li><span class='caret caret-down'> Page $Npage </span><ul class='nested active'>";
      }
      if ($Nsection != $section) {
        $section = $Nsection;
        if (!$firstS)
          echo "</ul></li>";
        else
          $firstS = false;
        echo "<li><span class='caret'> Section $section </span><ul class='nested'>";
      }
      echo "<li>$desc</li>";
    }
    echo "</ul>";
    ?>
  </div>

  <div id=GPXmanagement>

    <div class="formContainer">
      <form enctype="multipart/form-data" action="index.php?page=Admin&pageModif=Acceuil&section=2" method="post">
        <div class="mb-3">
          <input type="hidden" name="MAX_FILE_SIZE" value="30000000" />
          <label for="trace" class="form-label">Trace GPX numéroté (par exemple: trace10.gpx)</label>
          <input type="file" class="form-control" name="trace" id="trace" />
        </div>
        <button type="submit" class="btn btn-primary">Envoyer</button>
      </form>
    </div>

    <div class="formContainer">
      <form enctype="multipart/form-data" action="index.php?page=Admin&pageModif=Acceuil&section=2" method="post">
        <div class="mb-3">
          <input type="hidden" name="MAX_FILE_SIZE" value="30000000" />
          <label for="loader" class="form-label">Dossier contenant toutes les traces GPX numérotées (par exemple: trace10.gpx)</label>
          <input type="file" class="form-control" name="traces[]" id="loader" webkitdirectory multiple />
        </div>
        <button type="submit" class="btn btn-primary">Envoyer</button>
      </form>
    </div>

    <button>Supprimer toutes les traces enregistrées
      Requete à configurer
      <?php
      /*  GPX::removeGPX(); */
      ?>
    </button>

    <button>Calculer les horaires de passage
      Requete à configurer
      <?php
      $select = $conn->query("SELECT contenu, sous_section from content where page='Troncons' and section=1");
      while ($horaire = $select->fetch()) {
        if ($horaire["sous_section"] == 1)
          $hdep = $horaire["contenu"];
        else
          $harr = $horaire["contenu"];
      }
      $duration = $harr-$hdep;
      $select = $conn->query("SELECT * from tracesGPX");
      $n = $select->rowCount();
      $delta = $duration/$n;
      $current_delta = 0;
      for ($i=1; $i <= $n; $i++) { 
        $update = $conn->prepare("UPDATE tracesGPX set heure_dep=FROM_UNIXTIME(?), heure_arr=FROM_UNIXTIME(?) where id =?");
        $update->execute(array($hdep+$current_delta, $hdep+$current_delta+$delta, $i));
        $current_delta+=$delta;
      }
      ?>

    </button>
  </div>
</section>
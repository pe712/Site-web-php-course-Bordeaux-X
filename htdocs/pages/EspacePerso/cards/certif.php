<?php
if ($user->chauffeur == null) {
    echo '<p class="espacePerso-firstLine">Vous devez d\'abord remplir la catégorie informations personnelles.</p>';
} elseif ($user->chauffeur) {
    echo '<p class="espacePerso-firstLine">Vous n\'avez pas besoin de renseigner de certificat.</p>';
} else {
    if ($user->certificat) {
        echo '<div class="centerer-container" style="display: none;" id="espacePerso-certificatUpload">';
    } else {
        echo '<div class="centerer-container" id="espacePerso-certificatUpload">';
    }
?>
    <form enctype="multipart/form-data" action="?page=EspacePerso" method="post">
        <div class="mb-3">
            <input type="hidden" name="MAX_FILE_SIZE" value="500000" />
            <label for="certif_uploaded" class="form-label">Certificat médical de moins de 1 an</label>
            <input type="file" class="form-control" name="certificat" id="certif_uploaded" />
        </div>
        <button id="espacePerso-certif-button" type="submit" class="btn btn-primary">Envoyer</button>
    </form>
    </div>
    <?php
    if ($user->certificat) {
        $path = $dossier . $name;
    ?>
        <p id="espacePerso-messageCertif" class="espacePerso-firstLine">Vous avez déjà mis votre certificat médical. Cliquez
            <a href="" id="espacePerso-download" download><span hidden><?= $path ?></span>ici</a> pour le voir et
            <a href="" id="espacePerso-modifyCertif">ici</a> pour le modifier
        </p>
<?php
    }
} ?>

<button class="btn btn-primary" onclick="changeView('certif', 'cards')" id="espacePerso-retourFromCertif">Retour</button>
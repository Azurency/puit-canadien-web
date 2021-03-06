<?php session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>

  <?php
    $title = "Administration";
    include('includes/layout/head.php');
    include('admin/GestionBase.php');
  ?>

  <!-- POUR LA SELECTION A VOIR SI IL FAUT GARDER -->
  <link rel="stylesheet" href="assets/vendor/jquery/jquery-ui.css">
  <link rel="stylesheet" href="assets/vendor/jquery/jquery.ui.theme.css">
  <script type="text/javascript" src="assets/vendor/jquery/jquery-ui.js"></script>
  <!-- !! -->

  <script type="text/javascript" src="assets/vendor/tabs/js/easyResponsiveTabs.js"></script>
  <link rel="stylesheet" href="assets/vendor/tabs/css/easy-responsive-tabs.css">

  <style>
    .ui-selecting { background: #FECA40; }
    .ui-selected { background: #F39814; color: white; }
  </style>

  <script type="text/javascript">
    $(document).ready(function () { 
      $(function() {
        $( "#selectable" ).selectable();
        $( "#selectable2" ).selectable();
        $("#selectable3").selectable();
      });

    $('#supprCapteur').click(function () {
      $('#selectable .ui-widget-content.ui-selected').each(function(index) {
        var tmp = $(this).attr('data-userid');
        $.post('admin/supprCapteur.php', { id : tmp })
          .done(function (data) { location.reload(); })
          .fail(function (data) {});
      });
    });

    $('#supprDispositif').click(function () {
      $('#selectable2 .ui-widget-content.ui-selected').each(function(index) {
        var tmp = $(this).attr('data-userid');
        var all = 'non';
        if (document.getElementById('tout').checked) {
		  all = 'oui';
	    }
        $.post('admin/supprDispositif.php', { id : tmp, tout : all })
          .done(function (data) { location.reload(); })
          .fail(function (data) {});
      });
    });

    $('#supprArduino').click(function () {
      $('#selectable3 .ui-widget-content.ui-selected').each(function(index) {
        var tmp = $(this).attr('data-userid');
        $.post('admin/supprArduino.php', { id : tmp })
          .done(function (data) { location.reload(); })
          .fail(function (data) {});
      });
    });

    <?php
      $res = infoDispositif();
      while($data = $res->fetch(PDO::FETCH_ASSOC)) {
        echo "$('#sondeCorbeille').append('<option value=" . $data['idD'] . ">" . $data['nomD'] . "');\n";
      }

    ?>
    });
  </script>
</head>

<body>
  <?php include('includes/layout/header.php'); ?>

  <?php include("admin/pass.php"); ?> <!-- Verifie le mot de passe de l'admin -->

  <div class="container">
  <div class="wrapper">
  <h2>Administration</h2>
    <?php
    if($_SESSION['pass'] == false) {
    ?>

    <div class="login-box four columns offset-by-four">
      <p>Veuillez entrer le mot de passe !</p>
      <form style="text-align: center" action="#" method="post">
        <input type="password" name="pass" class="u-full-width" value=""/>   
        <input type="submit" value="Valider"/>
      </form>
    </div>

    <?php } else { ?>

    <div id="parentTab">
      <ul class="resp-tabs-list hor_1">
        <li>Gérer capteur</li>
        <li>Gérer dispositifs</li>
        <!-- A FAIRE : Lire les .dat dans Importer/Exporter -->
        <!-- <li>Impoter/Exporter</li>  -->
        <li>Arduino</li>
      </ul>
      <div class="resp-tabs-container hor_1">
        <div>
          <br>
          <form action="admin/ajouterCapteur.php" method="post">
            <h6>Ajouter un capteur</h6>
            <div class="row">
              <div class="six columns">
                <label for="nom">Nom du capteur : </label>
                <input type="text" required="required" class="u-full-width" name="nom" id="nom">
              </div>
              <div class="six columns">
                <label for="relier">Dispositif parent : </label>
                <select name="relier" required="required" class="u-full-width" id="sondeCorbeille"></select>
              </div>
            </div>
            <div class="row">
			  <div class="six columns">
				<label for="type">Type de capteur :</label>
                <select name="type" required="required" class="u-full-width">
                  <option value="A">Analogique</option>
                  <option value="N">Numerique</option>
                </select>
			  </div>
			  <div class="six columns">
			    <label for="unite">Unite du capteur :</label>
			    <select name="unite" required="required" class="u-full-width">
                  <option value="tempe">Temperature °C</option>
                  <option value="press">Pression</option>
                </select>
			  </div>
            </div>
            <div class="row">
              <div class="four columns">
                <label for="posx">Longueur (axe X) :</label>
                <input type="text" required="required" class="u-full-width" name="posx" id="posx">
              </div>
              <div class="four columns">
                <label for="posz">Largeur (axe Z) :</label>
                <input type="text" required="required" class="u-full-width" name="posz" id="posz">
              </div>
              <div class="four columns">
                <label for="posy">Hauteur (axe Y) :</label>
                <input type="text" required="required" class="u-full-width" name="posy" id="posy">
              </div>
              <input type="submit" value="Ajouter" class="button-primary u-pull-right" />
            </div>
          </form>
          <hr>
          <h6>Supprimer des capteurs</h6>
          <div class="tabs-listeSondes">
            <ul id="selectable">
            <?php
              $res = infoCapteur();
              while($data = $res->fetch(PDO::FETCH_ASSOC)) {
                echo '<li class="ui-widget-content" data-userid="' . $data['idC'] . '">' . $data['nomC'] . '</li>';
              }
            ?>
            </ul>
          </div>
          <button type="button" class="button-primary u-pull-right" id="supprCapteur">Supprimer</button>
          <div class="u-cf"></div>
        </div>
        <div>
          <br>
          <form action="admin/ajouterDispositif.php" method="post">
            <h6>Ajouter un dispositif</h6>
            <div class="row">
              <div class="six columns">
                <label for="nom">Nom du dispositif :</label>
                <input type="text" class="u-full-width" name="nom" id="nom">
              </div>
              <div class="six columns">
                <label for="type">Type du dispositif :</label>
                <select name="type" class="u-full-width">
                  <option value="corbeille">Corbeille</option>
                  <option value="puit canadien">Puit Canadien</option>
                  <option value="sonde">Sonde</option>
                  <option value="autre">Autre</option>
                </select>
              </div>
            </div>
            <div class="row">
              <div class="twelve columns">
                <label for="lieu">Lieu :</label>
                <select name="lieu" class="u-full-width">
                  <option value="devantGTE">Devant le batiment GTE</option>
                  <option value="cafeteria">À côté de la cafétéria</option>
                </select>
              </div>
            </div>
            <div class="row">
              <div class="four columns">
                <label for="posx">Longueur (axe X) :</label>
                <input type="text" required="required" class="u-full-width" name="posx" id="posx">
              </div>
              <div class="four columns">
                <label for="posz">Largeur (axe Z) :</label>
                <input type="text" required="required" class="u-full-width" name="posz" id="posz">
              </div>
              <div class="four columns">
                <label for="posy">Hauteur (axe Y) :</label>
                <input type="text" required="required" class="u-full-width" name="posy" id="posy">
              </div>
              <input type="submit" value="Ajouter" class="button-primary u-pull-right" />
            </div>
          </form>
          <hr>
          <h6>Supprimer des dispositifs</h6>
          <div class="tabs-listeSondes">
            <ul id="selectable2">
              <?php
                $res = infoDispositif();
                while($data = $res->fetch(PDO::FETCH_ASSOC)){
                  echo '<li class="ui-widget-content" data-userid="' . $data['idD'] . '">' . $data['typeD'] . ' -- ' . $data['nomD'] . '</li>';
                }
              ?>
            </ul>
          </div>
          <input type="checkbox" name="tout" id="tout" value="true" /> Supprimer les capteurs liés aux dispositifs.
          <button type="button" class="button-primary u-pull-right" id="supprDispositif">Supprimer</button>
          <div class="u-cf"></div>
        </div>
        <!-- <div>
          <br>
          <h6>Import d'un fichier .sql</h6>
          <form name="import" enctype="multipart/form-data" action="admin/backupBase.php" method="post">
            <div class="row">
              <div class="twelve columns">
                <label for="file">Fichier .sql:</label>
                <input type="file" class="u-full-width" name="sql">
              </div>
            </div>
            <input type="hidden" name="action" value="import">
            <input type="hidden" name="MAX_FILE_SIZE" value="300000">
            <input type="submit" class="button-primary u-pull-right" value="Valider">
            <br>
          </form>
          <h6>Export de la base :</h6>
          <form name="export" action="admin/backupBase.php" method="post">
            <div class="row">
              <div class="twelve columns">
                <label for="filename">Nom du fichier :</label>
                <input type="text" name="filename" class="u-full-width">
              </div>
            </div>
            <input type="hidden" name="action" value="export">
            <input type="submit" class="button-primary u-pull-right" value="Valider">
          </form>
          <div class="u-cf"></div>
        </div> -->
        <div>
          <br>
          <h6>Ajouter un arduino</h6>
          <form name="chgFreq" action="admin/ajouterArduino.php" method="post">
            <div class="row">
              <div class="six columns">
                <label for="nom">Nom de l'arduino :</label>
                <input type="text" class="u-full-width" name="nom" id="nom">
              </div>
              <div class="six columns">
                <label for="address">Adresse Mac :</label>
                <input type="text" class="u-full-width" name="address" id="address">
              </div>
            </div>
            <div class="row">
              <div class="six columns">
                <label for="nbAnalogique">Nombre de ports analogiques :</label>
                <input type="text" class="u-full-width" name="nbAnalogique" id="nbAnalogique">
              </div>
              <div class="six columns">
                <label for="nbNumerique">Nombre de ports numériques :</label>
                <input type="text" class="u-full-width" name="nbNumerique" id="nbNumerique">
              </div>
            </div>
            <input type="submit" class="button-primary u-pull-right" value="Ajouter">
          </form>
          <div class="u-cf"></div>
          <h6>Supprimer des arduino</h6>
          <div class="tabs-listeSondes">
            <ul id="selectable3">
            <?php
              $res = infoArduino();
              while($data = $res->fetch(PDO::FETCH_ASSOC)) {
                echo '<li class="ui-widget-content" data-userid="' . $data['idA'] . '">' . $data['nom'] . '</li>';
              }
            ?>
            </ul>
          </div>
          <button type="button" class="button-primary u-pull-right" id="supprArduino">Supprimer</button>
          <div class="u-cf"></div>
          <h6>Création des branchements</h6>
          <a href="arduino.php">Accèder à l'interface de modification des branchements</a>
        </div>
      </div>
    </div>
    <?php } ?>
    <div class="push"></div>
    <div class="push"></div>
  </div> <!-- wrapper -->
  </div> <!-- container -->

<?php include('includes/layout/footer.php');  ?>

  <script type="text/javascript">
    $(document).ready(function() {
        //Horizontal Tab
        $('#parentTab').easyResponsiveTabs({
            type: 'default', //Types: default, vertical, accordion
            width: 'auto', //auto or any width like 600px
            fit: true, // 100% fit in a container
            tabidentify: 'hor_1', // The tab groups identifier
            active_border_color: '#e1e1e1', // border color for active tabs heads in this group
            active_content_border_color: '#E94F51',
            activate: function(event) { // Callback function if tab is switched
                var $tab = $(this);
                var $info = $('#nested-tabInfo');
                var $name = $('span', $info);
                $name.text($tab.text());
                $info.show();
            }
        });
    });
  </script>

</body>
</html>

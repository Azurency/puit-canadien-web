<!DOCTYPE html>
<html lang="fr">
  <head>
    <?php
      $title = "Accueil";
      include('includes/layout/head.php');
      include("admin/ConnexionBD.php");
      include("includes/scripts/generer_graph.php");
    ?>
    <link rel="stylesheet" href="assets/css/datepicker.css">

    <!-- SCRIPTS
    –––––––––––––––––––––––––––––––––––––––––––––––––– -->
    <script src="assets/vendor/d3/d3.min.js" charset="utf-8"></script>
    <script src="assets/vendor/c3/c3.min.js"></script>
    <!--<script src="assets/vendor/highcharts/highcharts.js"></script>-->
    <script src="assets/vendor/highcharts/highstock.js"></script>
    <script src="assets/vendor/highcharts/exporting.js"></script>
    <script src="assets/js/datepicker.js"></script>
    <script src="assets/js/datepicker.fr.js"></script>

  </head>
  <body>
    <div class="container">

      <?php include('includes/layout/header.php'); ?>

      <!-- Page Content - Boxes
      –––––––––––––––––––––––––––––––––––––––––––––––––– -->
      <div class="boxes">

        <h2>Générer un graphe</h2>

        <section>
          <h5 class="section-header">Filtres</h5>
          <div class="box">
            <div class="box-section">
              <p>Utilisez la vue 3D pour sélectionner des sondes</p>
              <iframe id="iframe" class="u-full-width" src="includes/layout/vue3D.php"></iframe>
            </div>
            <hr>
            <div class="box-section">
              <h6>Sondes sélectionnées : </h6>
              <div id="selected-sonde">
                Aucune sonde sélectionnée.
              </div>
            </div>
            <hr>
            <div class="box-section">
              <form action="index.php" id="generate-graph-form" method="POST">
                <h6>Afficher sur une période de :</h6>
                <div class="row input-daterange" id="datepicker">
                  <div class="one-half column">
                    <label for="dateDebut">Date de début</label>
                    <input class="u-full-width input-sm" value="10/01/2015" type="text" id="dateDebut" name="dateDebut" size="18" name="start" >
                  </div>
                  <div class="one-half column">
                    <label for="dateFin">Date de fin</label>
                    <input class="u-full-width input-sm" value="16/01/2015" type="text" id="dateFin" name="dateFin" size="18" name="end" >
                  </div>
                </div>
                <div class="row">
                  <div class="twelve columns">
                    Sondes à selectionner :
                    <input type="checkbox" name='capteurs[]' value="A1-1"> A1-1
                    <input type="checkbox" name='capteurs[]' value="A1-2.5"> A1-2.5
                    <input type="checkbox" name='capteurs[]' value="A1-4"> A1-4
                    <input type="checkbox" name='capteurs[]' value="A2-1"> A2-1
                    <input class="button-primary u-full-width" type="submit" name="generer" value="Génerer">  
                  </div>
                </div>
              </form>
            </div>
          </div>
        </section>

        <section>
          <h5 class="section-header">Graphique des températures</h5>
          <div class="box">
            <div class="box-section">
              <?php valider() ?> <!-- Appelle generer_graph.php-->
              <div id="chart"></div>
              <div id="test"></div>
            </div>
          </div>
        </section>

        <section>
          <h5 class="section-header">Statistiques</h5>
          <div class="box">
            <div class="box-section">
              <table class="u-full-width">
                <thead>
                  <tr>
                    <th>Sonde</th>
                    <th>min</th>
                    <th>max</th>
                    <th>ecart-type</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td><canvas class="sonde1-canevas" width="10" height="10"></canvas> Sonde 2m</td>
                    <td>9</td>
                    <td>25</td>
                    <td>18</td>
                  </tr>
                  <tr>
                    <td><canvas class="sonde2-canevas" width="10" height="10"></canvas> Sonde 3m</td>
                    <td>5</td>
                    <td>12</td>
                    <td>8</td>
                  </tr>
                  <tr>
                    <td><canvas class="sonde3-canevas" width="10" height="10"></canvas> Sonde 1.5m</td>
                    <td>1</td>
                    <td>8</td>
                    <td>3</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </section>
      </div>
    </div>
    <div class="push"></div>
    <?php include('includes/layout/footer.php'); ?>
    <?php include("assets/js/graph.php"); ?>
    <script>

      $(document).ready(function() {
        $('#iframe').height($('#iframe').width() / ($(window).width() / $(window).height()));
      });

      $('#datepicker').datepicker({
        format: "dd/mm/yyyy",
        weekStart: 1,
        todayBtn: "linked",
        language: "fr"
      });

      $('.sonde1-canevas').each(function(index) {
        var canvas = this;
        var context = canvas.getContext('2d');
        context.rect(0, 0, 10, 10);
        context.fillStyle = '#27636D';
        context.fill();
      });

      $('.sonde2-canevas').each(function(index) {
        var canvas = this;
        var context = canvas.getContext('2d');
        context.rect(0, 0, 10, 10);
        context.fillStyle = '#FA7577';
        context.fill();
      });

      $('.sonde3-canevas').each(function(index) {
        var canvas = this;
        var context = canvas.getContext('2d');
        context.rect(0, 0, 10, 10);
        context.fillStyle = '#C5DFA2';
        context.fill();
      });

      // fonction appelée lors de la reception d'un message de l'iframe
      function receiveMessage(event) {
        // match un message de la forme "fonction:nom,id"
        var match = event.data.match("(^[a-z]*):([^,]+),(.+)");
        if (match[1]=="selected") {
          // si le div contient le texte "Aucune sonde sélectionnée.", on le vide
          if ($('#selected-sonde:contains("Aucune sonde sélectionnée.")').length > 0) {
            $("#selected-sonde").empty();
          }
          // on ajoute le span de la sonde selectionnée, et le champ caché au formulaire
          $("#selected-sonde").append('<span class="sonde-selected" sonde-id="'+match[3]+'"><span> - </span><span class="sonde-color" style="background-color: #27636D;"></span> '+match[2]+'</span>\n')
          $("#generate-graph-form").append('<input type="hidden" name="capteursId[]" value="' + match[3] + '">');
        } else if (match[1]=="deleted") {
          // on supprimer les elt qui ont l'attribut sonde-id égal à l'id du message
          $('#selected-sonde > span[sonde-id="'+match[3]+'"]').remove();
          $('#generate-graph-form > input[type="hidden"][value="'+match[3]+'"]').remove();
          // si le div est vide, on remet le texte "Aucune sonde sélectionnée."
          if( !$.trim( $("#selected-sonde").html() ).length ){
            $("#selected-sonde").append('Aucune sonde sélectionnée.');
          }
        }
        // si le premier elt contient un " - ", il faut le supprimer
        if ($("#selected-sonde > span:first-child > span:first-child").text() == " - ") {
          $("#selected-sonde > span:first-child > span:first-child").remove();
        }
      }

      // listener du message de l'iframe
      if (window.addEventListener) {
        window.addEventListener('message', receiveMessage, false);
      } else if (window.attachEvent) { // pour IE8
        window.attachEvent('onmessage', receiveMessage);
      }

    </script>
  </body>
</html>

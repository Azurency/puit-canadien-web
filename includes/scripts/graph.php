<?php 
  $idCapt = isset($_POST["capteursId"]) ? $_POST["capteursId"] : [1,7,10,16,19,22];
?>
<script>
var captParDefaut = true;

$.ajaxSetup({'async': false});

function getDateDebut() {
  var dateDebutBrut = $("input[name=dateDebut]").val();
  var dateDebutJ = dateDebutBrut.split("/")[0];
  var dateDebutM = dateDebutBrut.split("/")[1] - 1;
  var dateDebutA = dateDebutBrut.split("/")[2];
  return new Date(dateDebutA, dateDebutM, dateDebutJ, 0,0,0,0);
}

function getDateFin() {
  var dateFinBrut = $("input[name=dateFin]").val();
  var dateFinJ = dateFinBrut.split("/")[0];
  var dateFinM = dateFinBrut.split("/")[1] - 1;
  var dateFinA = dateFinBrut.split("/")[2];
  return new Date(dateFinA, dateFinM, dateFinJ, 0,0,0,0);
}

function addSerie(id, name) {
  var chart = $('#chart').highcharts();

  chart.showLoading('Chargement des données...');

  if(captParDefaut) {
    $('#no-selection').hide('400');
    $('#chart').show('400');

    var dateD = getDateDebut();
    var dateF = getDateFin();
    captParDefaut = false;

    // Si aucune sonde n'a encore été selectionnée, les capteurs par défaut doivent être effacé
    var seriesLength = chart.series.length;
    for(var i = seriesLength - 1; i > -1; i--) {
        if(chart.series[i].name.toLowerCase() != 'navigator') {
            chart.series[i].remove();
        }
    }

    // On ajoute les données de la sonde sélectionnée et on met à jour les axes
    $.getJSON('includes/scripts/getDataChart.php?id=' + id 
              + '&start=' + Math.round(dateD.getTime()) 
              + '&end=' + Math.round(dateF.getTime()) 
              + '&callback=?', function (data) {
      var nav = chart.get('highcharts-navigator-series');
      chart.addSeries({
        name : name,
        data: data
      });
      chart.xAxis[0].setExtremes(dateD.getTime(), dateF.getTime());
      nav.setData(data);
      nav.xAxis.setExtremes(dateD.getTime(), dateF.getTime());
    });
  } else {
    // On ajoute les données de la sonde sélectionnée sans changer les extremes
    $.getJSON('includes/scripts/getDataChart.php?id=' + id 
              + '&start=' + Math.round(chart.xAxis[0].getExtremes().min - (60000*60)) 
              + '&end=' + Math.round(chart.xAxis[0].getExtremes().max - (60000*60)) 
              + '&callback=?', function (data) {
      var nav = chart.get('highcharts-navigator-series');
      chart.addSeries({
        name : name,
        data: data
      });
    });
  }

  chart.hideLoading();
}

function changeExtremes(date, isDebut) {
  var chart = $('#chart').highcharts();
  var nav = chart.get('highcharts-navigator-series');
  var dateD, dateF;

  if (date == 0) { // si on clique sur le bouton générer
    dateD = getDateDebut();
    dateF = getDateFin();
  } else if (isDebut) { // si on veux modifier la date de début
    dateD = date;
    dateF = getDateFin();
  } else { // si on veux modifier la date de fin
    dateD = getDateDebut();
    dateF = date;
  }

  $("input[name='capteursId[]']").each(function(index) {
    var name = $(this).attr("sonde-name");

    chart.showLoading('Chargement des données...');
    $.getJSON('includes/scripts/getDataChart.php?id='+ $(this).attr("value") 
              + '&start=' + Math.round(dateD.getTime()) 
              + '&end=' + Math.round(dateF.getTime()) 
              + '&callback=?', function (data) {
      for (var i = 0; i < chart.series.length; i++) {
        if(chart.series[i].name.toLowerCase() == name.toLowerCase()){
          chart.series[i].setData(data);
          break;
        }
      };
      chart.hideLoading();
      chart.xAxis[0].setExtremes(dateD.getTime(), dateF.getTime());
      nav.setData(data);
      nav.xAxis.setExtremes(dateD.getTime(), dateF.getTime());
    });

  });
}

function removeSerie(data) {
  var chart = $('#chart').highcharts();
  for (var i = 0; i < chart.series.length; i++) {
    if(chart.series[i].name.toLowerCase() == data.toLowerCase()){
      chart.series[i].remove();
    }
  }
  // Si il n'y a plus de capteurs sélectionné, on remet le boolean à true
  if (chart.series.length == 1) {
    console.log("tout les graphes sont supprimés");
    captParDefaut = true;
    $('#chart').hide('400');
    $('#no-selection').show('400');
  }
}

function afterSetExtremes(e) {
  var chart = $('#chart').highcharts();
  var nav = chart.get('highcharts-navigator-series');

  $("input[name='capteursId[]']").each(function(index) {
    var name = $(this).attr("sonde-name");

    chart.showLoading('Chargement des données...');
    $.getJSON('includes/scripts/getDataChart.php?id=' + $(this).attr("value")
              + '&start=' + Math.round(e.min - (60000*60)) 
              + '&end=' + Math.round(e.max - (60000*60)) 
              + '&callback=?', function (data) {
      for (var i = 0; i < chart.series.length; i++) {
        if(chart.series[i].name.toLowerCase() == name.toLowerCase()){
          chart.series[i].setData(data);
          break;
        }
      };
      chart.hideLoading();
    });
  });
}


var options = { series: [] };
var dateD = getDateDebut();
var dateF = getDateFin();

<?php foreach ($idCapt as $idCC) { ?>

  $.getJSON('includes/scripts/getDataChart.php?id=<?php echo $idCC ?>'
            + '&start=' + Math.round(dateD.getTime()) 
            + '&end=' + Math.round(dateF.getTime()) 
            + '&callback=?', function (data) {
    options.series.push({
      name : '<?php echo getCapteurNameById($idCC);?>',
      data: data
    });
  });

<?php } ?>




var oppppt = {
                chart : {
                    zoomType: 'x'
                },
                navigator : {
                    enabled: true,
                    adaptToUpdatedData: false,
                    xAxis: {
                        ordinal: false
                    }
                },
                title: {text: ' '},
                rangeSelector : {
                    buttons: [{
                        type: 'hour',
                        count: 1,
                        text: '1h'
                    }, {
                        type: 'day',
                        count: 1,
                        text: '1d'
                    }, {
                        type: 'month',
                        count: 1,
                        text: '1m'
                    }, {
                        type: 'year',
                        count: 1,
                        text: '1y'
                    }, {
                        type: 'all',
                        text: 'All'
                    }],
                    inputEnabled: false, // it supports only days
                    selected : 4 // all
                },
                xAxis : {
                    events : {
                        afterSetExtremes : afterSetExtremes
                    },
                    ordinal : false,
                    minRange: 3600 * 1000 // one hour
                }
            };

    oppppt.series = options.series;

    $('#chart').highcharts('StockChart', oppppt);

    $(function() {
      // Met le graphique des capteurs par défaut à la bonne date quand le document est prêt
      $('#chart').highcharts().xAxis[0].setExtremes(dateD.getTime(), dateF.getTime());
      $('#chart').highcharts().get('highcharts-navigator-series').setData(options.series[0].data);
      $('#chart').highcharts().get('highcharts-navigator-series').xAxis.setExtremes(dateD.getTime(), dateF.getTime());
    });


</script>

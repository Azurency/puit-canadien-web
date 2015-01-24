
<script>

/*
var tempO = [-2, -2.6, -2.5, -2.2, -4.7, -4.2, -3.7, -4.2, -3.8, -4.4, -2, -0.6, 1.6, 2, 2.6, 2.6, 2.1, 1.4, 0, -0.7, -1.2, -1.1, -1.2, -0.9]
var temp = [3.1, 3.1, 3.9, 4, 4, 3, 3.7, 3.6, 2.2, 2.4, 2.4, 3, 1.5, 2, 1.1, 1.7, 1.8, -0.3, -0.5, 0.2, -0.1, 0.7, -0.4, -0.3];
var tempP=[];
//for (var i = 0; i < 290; i++) { tempP.push((Math.random()*10)-5);}; // création  de tableau random
for (var i = 0; i < 5; i++) {
	var tab=[];
	for (var j = 0; j < 5; j++) {
		tab.push((Math.random()*10)-5);
	};
	tempP.push(tab);
};

*/

$('#chart').highcharts('StockChart', {

		chart: {
			zoomType: 'xz', // x tout seul ne marche pas, ça marche avec z ... pourquoi ? bonne question
			resetZoomButton:{enabled:true},
			
		},

		navigator: {enabled: false}, // enlever la mini chart

		title: {text: ' '}, // pour ne pas avoir de titre ' ' 
	
	    xAxis: {
            type: 'candlestick',
            dateTimeLabelFormats: {
            	second: '%H:%M:%S',
            },
            title: {text: 'Temps'},
        },

	    yAxis: { title: { text:'Temperature (°C)' }},

	    // petite fenetre de valeur
	    tooltip: {
	    	xDateFormat: "%d %b %Y, %H:%M:%S",
	    	valueDecimals: 2, 
	    	valueSuffix: '°C', 
	    },
	
		plotOptions: {
            series: {
                pointStart: Date.UTC(2015, 0, 20, 0, 0),
                pointInterval: 900000, // mettre en ms : http://unit-converter.org/fr/temps.html
            }
        },

        legend: {enabled: true}, // pdf pas de legend sur H.Stock

        rangeSelector: {

        		enabled: true,

        		// le style des bouton
        		buttonTheme: {
        			fill: 'none',
                	stroke: 'none',
                	'stroke-width': 0,
                	r: 8,
                	style: {
                	    color: '#E94F51',
                	    fontWeight: 'bold'
                	},
                	states: {
                	    hover: {
                	    },
                	    select: {
                	        fill: '#E94F51',
                	        style: {
                	            color: 'white'
                	        }
                	    }
                	}
        		},

        		// champs de date à remplir
        		inputDateFormat: '%d-%m-%Y',
        		inputDateParser: '%d-%m-%Y',
        		inputEditDateFormat: '%d-%m-%Y',
        		inputBoxBorderColor: 'gray',
	            inputBoxWidth: 120,
	            inputBoxHeight: 18,
	            inputStyle: {
	                color: '#E94F51',
	                fontWeight: 'bold'
	            },
	            labelStyle: {
	                color: 'silver',
	                fontWeight: 'bold'
	            },
	            selected: 1,

        		// les boutons de intervalle
                buttons: [{
                    type: 'hour',
                    count: 1,
                    text: '1h'
                	}, {
                    type: 'day',
                    count: 1,
                    text: '1j'
                	}, {
                    type: 'month',
                    count: 1,
                    text: '1m'
                	}, {
                    type: 'year',
                    count: 1,
                    text: '1a'
                	}, {
                    type: 'all',
                    text: 'All'
                	}],
                	inputEnabled: true, // avoir les champs "from...to..."
                	selected : 4 // all
            	},


	    series: [],
});

<?php 

$name = 0;
	foreach (generer_graph() as $courbe) {
		$dataC = "[";
		foreach ($courbe as $data) {
			if($dataC != "["){
				$dataC = $dataC . " , ";
			}
			$dataC = $dataC . $data; 
		}
		$dataC = $dataC."]";


		?>

	$('#chart').highcharts().addSeries({ name: 'courbe '+ <?php echo $name;?>,
										data:<?php echo $dataC;?>},
										true);

<?php
$name +=1;
	}

?>

</script>

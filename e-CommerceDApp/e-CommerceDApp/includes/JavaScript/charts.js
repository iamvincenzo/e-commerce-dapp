/**
 * 
 * @author Vincenzo Fraello (299647) - Lorenzo Di Palma (299636) 
 *
 */

$(document).ready(function () {

    $.ajax({ // richiesta AJAX effettuata per richiedere le informazioni statistiche sulle vendite dei prodotti

        url: 'http://bookstore/viewStats',

        success: function (response) {

            jArray = JSON.parse(response);

            console.log(jArray);

            let myCanvas = document.getElementById("myCanvas").getContext('2d');

            let myLabels = [];

            let myData = [];

            for (var i = 0; i < jArray.length; i++) {

                myLabels.push(jArray[i].Titolo);
                myData.push(jArray[i].Percentuale);
            }

            let chart = new Chart(myCanvas, { // grafico e relativi dettagli

                type: 'bar', // line, pie (torta), horizontalBar, bar --> tipologie di grafico

                data: {

                    labels: myLabels,

                    datasets: [{

                        label: "Percentuale vendite libri",
                        data: myData,
                        backgroundColor: '#' + Math.floor(Math.random() * 16777215).toString(16),
                    }]
                },

                options: {

                    responsive: true,

                    maintainAspectRatio: false,

                    title: {

                        display: true,
                        text: 'Statistiche sulle vendite dei libri',
                        fontSize: 25,
                    },

                    tooltips: {
                        enabled: true
                    },

                    layout: {

                        padding: {
                            top: 20
                        }
                    },

                    scales: {
                        yAxes: [{
                            ticks: {
                                suggestedMin: 0,
                                suggestedMax: 100
                            }
                        }],
                        xAxes: [{
                            display: false
                        }]
                    }
                },
            });
        },

        async: true
    });
});

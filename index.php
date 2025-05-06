<!DOCTYPE html>
<html>
<head>
   <meta charset="UTF-8" />
   <meta name="viewport" content="width=device-width, initial-scale=1" />
   <title>Cloudflare (Custom)</title>
   <style>
      html,
      body {
         height: 100%;
         margin: 0;
      }
      body {
         display: flex;
         align-items: center;
         justify-content: center;
         flex-direction: column;
      }
      body > div {
         width: 100%;
      }
      #chart {
         width: 80%;
         margin: auto;
      }
      #info {
         margin-top: 2em;
         text-align: center;
         font-family: Arial, Helvetica, sans-serif;
      }
   </style>
</head>
<body>
   <div>
      <h2 id="info"></h2>
      <div id="chart"></div>
   </div>
   <script src="https://code.highcharts.com/highcharts.js"></script>
   <script src="https://code.highcharts.com/modules/exporting.js"></script>
   <script>
      window.previous = 0;
      window.hajime = true;
      window.onload = () => {
         let info = document.getElementById("info");

         Highcharts.createElement('link', {
            href: '//fonts.googleapis.com/css?family=Unica+One',
            rel: 'stylesheet',
            type: 'text/css',
         }, null, document.getElementsByTagName('head')[0]);

         var options = {
            plotOptions: {
               series: {
                  events: {
                     legendItemClick: function(event) {
                        event.preventDefault();
                     }
                  }
               }
            },
            chart: {
               zoomType: '',
               renderTo: "chart",
               style: {
                  fontFamily: "'Unica One', sans-serif",
               },
            },

            title: {
               text: '» Layer 7 DSTAT «',
               style: {
                  textTransform: 'uppercase',
                  fontSize: '20px',
               }
            },

            xAxis: {
               type: 'datetime',
               dateTimeLabelFormats: {
                  day: '%a'
               },
            },

            yAxis: {
               title: {
                  text: 'Requests/Sec',
                  margin: 80,
               }
            },

            credits: {
               enabled: false
            },

            exporting: {
               enabled: false
            },

            legend: {
               useHTML: true,
               symbolWidth: 0,
               labelFormatter: function () {
                  return '<div>' +
                     '<div style="display: inline-block; margin-right:5px"> </div>' +
                     "<span style='color: #c2c6dc;'> " + this.name +  " </span>" +
                     '</div>';
               }
            },

            subtitle: {
               style: {
                  color: '#c2c6dc',
                  font: 'bold 12px "Trebuchet MS", Verdana, sans-serif'
               }
            },

            series: [{
               type: 'area',
               name: 'Total Requests',
               color: '#FF69B4',
               data: []
            }]
         };

         chart = new Highcharts.Chart(options);

         info.innerText = "Live Layer 7 DSTAT Cloudflare (Custom) \n» https://" + location.host + " «";

         function update() {
            ajax = new XMLHttpRequest();
            ajax.onload = function(e) {
               part = ajax.responseText;
               var series = chart.series[0];
               console.log(part - window.previous);
               if (window.hajime !== true && part - window.previous > 0) {
                  series.addPoint([Math.floor(Date.now()), part - window.previous], true, series.data.length > 40);
               }
               window.hajime = false;
               window.previous = part;
            };
            ajax.onerror = function(e) {
               update();
            };
            ajax.ontimeout = function(e) {
               update();
            };
            ajax.open("GET", "get.php");
            ajax.send();
         }
         setInterval(update, 1000);
      };
   </script>
   <?php
      $counter_file = 'counter.txt';
      $counter_length = 8;
      $fp = fopen($counter_file, 'r+');
      if ($fp) {
         if (flock($fp, LOCK_EX)) {
            $counter = fgets($fp, $counter_length);
            $counter++;
            rewind($fp);
            if (fwrite($fp, $counter) === FALSE) {
               // error handling
            }
            flock($fp, LOCK_UN);
         }
         fclose($fp);
      }
   ?>
</body>
</html>

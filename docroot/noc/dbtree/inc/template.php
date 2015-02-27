<!DOCTYPE HTML>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Core Databases</title>
    <script type="text/javascript" src="https://code.jquery.com/jquery-1.9.1.js"></script>
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript" src='https://www.google.com/jsapi?autoload={"modules":[{"name":"visualization","version":"1","packages":["corechart","table","orgchart"]}]}'></script>

    <style>

    html {
        width: 100%;
        background: #eee;
    }

    body {
        font-family: Arial, sans-serif;
        width: 100%;
        padding: 0;
        min-width: 1024px;
        margin: 0;
        background: #eee;
        -webkit-text-size-adjust: none;
    }

    h1 {
        text-align: center;
    }

    #charts {
        background: #fff;
        border: 1px solid #ccc;
        padding: 1em;
    }

    #charts table {
        border-collapse: separate !important;
    }

    #charts > div {
        margin-bottom: 3em;
        white-space: nowrap;
    }

    #charts div.lag, #charts div.qps, #charts div.ver {
        font-size: smaller;
    }

    #charts div.lagging {
        color: red;
    }

    .google-visualization-orgchart-node {
        font-size: 100%;
        box-shadow: 2px 2px 2px #666;
        border-radius: 5px;
        padding: 0.5em;
        background: #eee;
        border: 1px solid #666;
    }

    .google-visualization-orgchart-node .stats {
        font-size: 80%;
    }

    .google-visualization-orgchart-node > a {
        text-decoration: none;
        color: blue;
    }

    .google-visualization-orgchart-node > a:visited {
        color: blue;
    }

    .google-visualization-orgchart-node > a.disabled {
        color: #999;
    }

    .google-visualization-orgchart-node > a.lagging {
        color: red;
    }

    </style>

</head>

<body>

    <h1>Core Databases</h1>

    <script type="text/javascript">

    google.setOnLoadCallback(drawChart);

    function drawChart()
    {
        <?php
        foreach ($clusters as $cluster)
        {
            ?>

            (function() {
                var data = new google.visualization.DataTable();

                data.addColumn('string', 'Host');
                data.addColumn('string', 'Master');

                data.addRows(
                    <?php print json_encode($cluster) ?>
                );

                var options = {
                    allowHtml: true
                };

                var div = $('<div></div>');
                $('#charts').append(div);

                var chart = new google.visualization.OrgChart(div.get(0));
                chart.draw(data, options);

            }());

        <?php
        }
        ?>
    }

    </script>

    <section role="main">

        <div id="charts">
        </div>

    </section>

</body>
</html>
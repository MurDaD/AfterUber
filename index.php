<?php
/**
 * Author: MurDaD
 * Author URL: https://github.com/MurDaD
 *
 * Description: Home page with form and Google Map
 */
?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
    <link rel="stylesheet" href="./css/style.css">
    <script type="text/javascript" src="//code.jquery.com/jquery-1.12.3.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    <script type="text/javascript" src="./js/script.js"></script>
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAa7iVqTx-TuSlOqs77fe1qrakKkLm9VN0&callback=initMap">
    </script>
</head>
<body>
<div class="container theme-showcase" role="main">
    <form id="request" class="bs-example">
        <div class="form-group">
            <label for="start_address">From:</label>
            <input type="text" class="form-control" name="start_address" id="start_address" placeholder="Address From" />
        </div>
        <div class="form-group">
            <label for="end_address">To:</label>
            <input type="text" class="form-control" name="end_address" id="end_address" placeholder="Address From" />
        </div>
        <input class="btn btn-primary" type="submit" value="Estimate" />
    </form>
    <br/><br/>
    <div class="loading"><p class="text-center"><img src="http://www.tbaf.org.tw/event/2016safe/imgs/loader1.gif"></p></div>
    <div class="providers row"></div>
    <br/><br/>
    <div id="map" width="100%"></div>
</div>
</body>
</html>


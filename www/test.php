<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
    <style type="text/css">
    body {font-family: Verdana; font-size: 1em; color: #888;}
    .alert {
     border: 1px solid #bbb;     
     padding: 16px;
     position: relative;
     top: 100;
     width: 300;
    }
    </style>
    <title>MiniHTTPD - Test</title>
  </head>
  <body>
    <center>
      <div class="alert">
        ... MiniHTTPD FastCGI test page ...
       <?php echo $_SERVER['PHP_SELF'].PHP_EOL ?>
      </div>
    </center>
  </body>
</html>

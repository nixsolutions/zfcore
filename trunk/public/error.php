<?php
/**
 * @category Public
 * @package  Bootstrap
 */
?>
<!DOCTYPE html>
<html dir="ltr" lang="en-US">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Critical Error</title>
</head>
<body>
    <h2>An exception occured while bootstrapping the application</h2>
    <p>Contact us information...</p>
    <?php
        if (APPLICATION_ENV == 'development' && isset($exception)) {
            echo '<br /><br />' . $exception->getMessage() . '<br />'
               . '<div align="left">Stack Trace:'
               . '<pre>' . $exception->getTraceAsString() . '</pre></div>';
        }
    ?>
</body>
</html>
<?php exit(1); ?>
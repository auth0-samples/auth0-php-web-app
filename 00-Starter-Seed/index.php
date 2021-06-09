<?php

use Auth0\SDK\Exception\StateException;

require __DIR__ . '/common.php';

$state = $auth0->getCredentials();

if (! $state) {
  try {
    // Attempt code exchange.
    $auth0->exchange();
  } catch (StateException $e) {
    // There was an error during code exchange. Abort session check.
    $state = null;
  }
}

if ($state !== null && $state->accessTokenExpired) {
  try {
    // Token has expired, attempt to renew it.
    $auth0->renew();
  } catch (StateException $e) {
    // There was an error during access token renewal. Clear the session.
    $auth0->clear();
    $state = null;
  }
}

// After callback, redirect to / to remove callback params and avoid invalid state errors if page is refreshed.
if ($auth0->getRequestParameter('code')) {
  header("Location: /");
  exit;
}

?>
<html>
    <head>
        <script src="http://code.jquery.com/jquery-3.1.0.min.js" type="text/javascript"></script>

        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- font awesome from BootstrapCDN -->
        <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">
        <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css" rel="stylesheet">

        <link href="public/app.css" rel="stylesheet">

    </head>
    <body class="home">
        <div class="container">
            <div class="login-page clearfix">
              <?php if(!$state): ?>
                <div class="login-box auth0-box before">
                  <img src="https://i.cloudup.com/StzWWrY34s.png" />
                  <h3>Auth0 Example</h3>
                  <p>Zero friction identity infrastructure, built for developers</p>
                  <a id="qsLoginBtn" class="btn btn-primary btn-lg btn-login btn-block" href="login.php">Sign In</a>
                </div>
              <?php else: ?>
                <div class="logged-in-box auth0-box logged-in" id="profileDropDown">
                  <h1 id="logo"><img src="//cdn.auth0.com/samples/auth0_logo_final_blue_RGB.png" /></h1>
                  <h2>
                    <img class="avatar" src="<?php echo $state->user['picture'] ?>"/>
                    <span>Welcome <span class="nickname"><?php echo $state->user['nickname'] ?></span></span>
                  </h2>
                  <textarea><?php htmlspecialchars(print_r($state->user)); ?></textarea>
                  <a id="qsLogoutBtn" class="btn btn-warning btn-logout" href="logout.php">Logout</a>
                </div>
              <?php endif ?>
            </div>
        </div>
    </body>
</html>

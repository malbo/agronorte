<?php

/**
 * dashboard.php Main Page.
 *
 * Copyright (C) 2022 Agronorte <alboresmariano@gmail.com>
 *
 * 
 * @package frontend.Agronorte
 * @author  Mariano Alborés <alboresmariano@gmail.com>
 */

namespace Agronorte;

// simple autoloader according standard PSR-0
require_once(realpath(dirname(__FILE__) . '/../tools/Autoload.php'));

use Agronorte\core\Configuration;
use Agronorte\domain\User;
use Agronorte\frontend\inc\Menu;
use Agronorte\tools\Additional;
use Agronorte\tools\Data;
use Agronorte\tools\Secure;
use Agronorte\tools\Utils;

// sessions handler / permissions
session_start();
$user       = User::load(['id' => (int) $_SESSION['session']['id']]);
$validation = Secure::permissions($user);
if (false === $validation['valid'])
{
    header('Location: ' . $validation['url']);
}

// needed data
$params = [];
$report = Data::report($user);

// needed for top
require_once(realpath(dirname(__FILE__) . '/inc/Top.php'));
?>
<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
<div class="wrapper">

  <!-- Preloader -->
  <?php echo Utils::preloader();?>

  <!-- Navbar -->
  <?php echo Menu::navigation();?>

  <!-- Main Sidebar Container -->
  <?php echo Menu::aside($user);?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-12">
            <h1 class="m-0"><?php echo $report['name'];?></h1>
          </div>
        </div>
      </div>
    </div>

    <!-- Main content -->
    <section class="content">
          <div class="container-fluid text-center">
            <?php echo $report['iframe'];?>
          </div>
    </section>
  </div>

  <!-- Main Footer -->
  <?php echo Menu::footer();?>
</div>

<!-- Scripts -->
<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<script src="js/adminlte.js"></script>
<script>
    // Read embed application token from Model
    var accessToken = "<?php echo $report['embedToken'];?>"; // @Model.EmbedToken.Token
    // Read embed URL from Model
    var embedUrl = "<?php echo $report['embedUrl'];?>"; // @Html.Raw(Model.EmbedUrl)
    // Read report Id from Model
    var embedReportId = "<?php echo $report['reportId'];?>"; // @Model.Id
    // Get models. models contains enums that can be used.
    var models = window['powerbi-client'].models;
    // Embed configuration used to describe the what and how to embed.
    // This object is used when calling powerbi.embed.
    // This also includes settings and options such as filters.
    // You can find more information at https://github.com/Microsoft/PowerBI-JavaScript/wiki/Embed-Configuration-Details.
    var config = {
        type: 'report',
        tokenType: models.TokenType.Embed,
        accessToken: accessToken,
        embedUrl: embedUrl,
        id: embedReportId,
        permissions: models.Permissions.All,
        settings: {
            filterPaneEnabled: false,
            navContentPaneEnabled: false
        }
    };
    // Get a reference to the embedded report HTML element
    var reportContainer = $('#embedContainer')[0];
    if ("@Model.Username" != "") {
        $("#RLS").prop('checked', true);
        $("#RLSdiv").show();
    }
    else
    {
        $("#RLS").prop('checked', false);
        $("#RLSdiv").hide();
    }
    if ("@Model.IsEffectiveIdentityRequired.GetValueOrDefault()" == "True") {
        $("#noRLSdiv").hide();
        $("#RLS").removeAttr("disabled");
        $("#RLS").change(function () {
            if ($(this).is(":checked")) {
                $("#RLSdiv").show(300);
            } else {
                $("#RLSdiv").hide(200);
            }
        });
    }
    else
    {
        $("#noRLSdiv").show();
    }
    $("#embedContainer").css("visibility", "hidden");
    // Embed the report and display it within the div container.
    var report = powerbi.embed(reportContainer, config);
    report.on("rendered", function(event) {
        $("#overlay").hide();
        $("#embedContainer").css("visibility", "visible");
        report.off("rendered");
    })
</script>

<?php
// needed for bottom
require_once(realpath(dirname(__FILE__) . '/inc/Bottom.php'));
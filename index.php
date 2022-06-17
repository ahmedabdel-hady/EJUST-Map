<?php if (file_exists('install')) : ?>
	<a href="install">Click here to start installation</a>
<?php 
exit;
endif; 
?>


<!DOCTYPE html>
<html lang="en" >
  <head>
    <meta charset="utf-8">
    <title>EJUST Map</title>
	<meta name="author" content="Ahmed Abdelhady" />
	<meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />

    <link href="common/css/bootstrap.min.css" rel="stylesheet">
    <link href="common/css/bootstrap-modal.css" rel="stylesheet">
    <link href="common/css/leaflet.css" rel="stylesheet">
	
	<?php if (getenv('DEVELOPMENT_ENV')): ?>
		<link href="!dev/common/css/map-elements.css" rel="stylesheet">
		<link href="!dev/common/css/common.css" rel="stylesheet">
		<link href="!dev/front/css/front.css" rel="stylesheet">
	<?php else: ?>
		<link href="common/css/map-elements.css" rel="stylesheet">
	    <link href="common/css/common.css" rel="stylesheet">
		<link href="css/front.css" rel="stylesheet">
	<?php endif; ?>
	
	
    <!--stylesheet with marker type colors dynamically generated-->
    <link href="./gate/css.php" rel="stylesheet"> 
	
    
    <script>
        var head_conf = { widths: [920] };
    </script>
    <script src="common/js/head.min.js"></script>
    
    <link rel="icon" href="favicon.png" />

  </head>

  <body>
        <!-- panel column -->
        <div id="panelColumn" class="invisible"  >

            <div id="panelNav" class="navbar" >
                <div class="navbar-inner">
                    <a class="brand" href="#"><img alt="logo" src="common/img/logo.png" /></a>
                    <ul class="nav">
                        <li class="active">
                            <a id="viewsTabLink" href="#viewsTab">View</a>
                        </li>
                        <li>
                            <a id="searchTabLink" href="#searchTab" >Search</a>
                        </li>
                    </ul>
                </div>
            </div>

                <!-- panel tabs -->
                <div id="panelTabs" class="tab-content" >
                  <div id="viewsTab" class="tab-pane fade in active">
						<span class="nav-header">Maps:</span>
						<span data-state="noMaps" >No maps.</span>
                        <div id="treeElement" >
                        </div>
							
    					 <div id="regionList" class="hide">
    						<span class="nav-header">Regions:</span>
    						   <ul id="regionListContent" class="nav nav-list"  > 
    						   </ul>
    					 </div>	

                        <div style="height: 30px;" ></div>
                  </div>
                  
                  <!-- advanced search tab -->
                  <div id="searchTab" class="tab-pane fade" >
				  
					<div class="tab-content" >
					
						<div id="srchFormTab" class="tab-pane active" >
						
							<form id="searchForm" >
								<div id="searchValidationAlert" class="alert alert-error">
									<a class="close" href="#">&times;</a>
									Please enter at least one searching criteria and enter minimum 3 chars.
								</div>
							  <fieldset>
								<label class="control-label" for="searchTypeSelect">Marker type:</label>
								<select id="searchTypeSelect" name="markerTypeId"  >
								</select>
								
                                <br/>
								<label class="control-label cbx-label" for="currentCbx">Search only in the current map:</label>
                   				<input id="currentCbx"  type="checkbox" />
                				
								<label class="control-label" for="titleInput">Title:</label>
								<input type="text" id="titleInput" name="title" >
								
								<div id="srchInputsContent">
								  <!-- ajax response depending on search type -->
								</div>
								
								<button type="submit" class="btn btn-info btn-small pull-right">Search</button>
							  </fieldset>
							</form>
							
						</div>
						
						<div id="srchResultsTab" class="tab-pane" >
							<button id="backSearchFormBtn" type="button" class="btn btn-small"><i class="icon-chevron-left"></i> Back to search form</button>
							<span id="resultsCount" class="pull-right"></span>
							<div id="srchResultsContent">
								<!-- ajax response, search -->							
							</div>
						</div>
					</div>
                  </div> 
                  
                </div><!-- end of panel tabs -->
                
        </div><!-- end of panel column -->
              
              
          <!-- map column -->
        <div id="mapColumn" class="invisible" >
				<a id="panelToggleBtn" href="#" class="btn-toggle close-panel"><i class="icon-chevron-left"></i></a>
                <div id="mapContainer" class="" >
                    
                  <div id="mapViewer" class="viewer" style="width: 100%; height: 100%;" ></div>
                  
                  <div class="left-border" ></div>
				  <div class="cfm-legend hide" >
				    <strong>Legend:</strong>
				    <ul class="unstyled"></ul>
				  </div>
				  
                <div class="cfm-info" > 
                    <strong>Current map:&nbsp;</strong>
                    <ul class="cfm-breadcrumb breadcrumb"></ul>
                </div>
				  
              </div> <!-- end of map column -->
              
        </div> <!-- end of fluid row -->

		
   <div id="messageModal" class="modal hide fade" data-backdrop="static" tabindex="-1" data-focus-on="button:first">
        <div class="modal-header">
            <h3 data-state="oldBrowser">Outdated browser detected</h3>
        </div>
        <div class="modal-body" data-state="oldBrowser">
            <p>You are using an outdated browser.</p>
			<p>Using your current browser you may have limited access to all features of this application.</p>
        </div>
        <div class="modal-footer">
            <button href="#" class="btn btn-primary" data-dismiss="modal">OK</button>
        </div>
    </div>

	<div id="longTextModal" class="modal hide fade" data-backdrop="false">
	  <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" >&times;</button>
		<h3></h3>
	  </div>
	  <div class="modal-body">
		<p></p>
	  </div>
	</div>
	
    <div class="notifications"></div>
    
    <div id="preloader"> 
    	<img alt="logo" src="common/img/logo.png" />
    	<div class="progress progress-striped active">
		  <div class="bar" style="width: 100%;"></div>
		</div>
    </div>

    <noscript>
        <div class="no-js">
            It appears that your browser has JavaScript disabled.<br />
            This Website requires your browser to be JavaScript enabled. 
        </div>
    </noscript>
	
	<script src="common/js/jquery-1.11.3.min.js"></script>
	<script src="common/js/bootstrap.js"></script> 
	<script src="common/js/bootstrap-adds.js"></script> 
	<script src="common/js/leaflet.js"></script> 
	<script src="common/js/jquery.jstree.js"></script> 
	<script src="js/jquery.address.js"></script> 
	<script src="js/jquery.columnizer.js"></script>
	<script src="common/js/common-plugins.js"></script>

	<?php if (getenv('DEVELOPMENT_ENV')): ?>
		<script src="!dev/common/js/leaflet-conf.js"></script>
		<script src="!dev/common/js/Templates.js"></script>
		<script src="!dev/common/js/serviceCtrl.js"></script>
		<script src="!dev/common/js/mapViewerCtrl.js"></script>
		<script src="!dev/common/js/modalCtrl.js"></script>
		<script src="!dev/common/js/viewCtrl.js"></script>
			
		<script src="!dev/front/js/front.js"></script>
	<?php else : ?>
		<script src="common/js/common.js"></script>
		<script src="js/front.js"></script>
	<?php endif; ?>
	

  </body>
</html>

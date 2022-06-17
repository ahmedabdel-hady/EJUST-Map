<!DOCTYPE html>
<html lang="en" >
  <head>
    <meta charset="utf-8">
    <title>EJUST - admin panel</title>
	<meta name="author" content="flexphperia.net" />
	<meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1" />

    <link href="../common/css/bootstrap.min.css" rel="stylesheet">
    <link href="../common/css/bootstrap-modal.css" rel="stylesheet">
    <link href="../common/css/leaflet.css" rel="stylesheet">
    <link href="css/bootstrap-datatables.css" rel="stylesheet">
    <link href="css/fineuploader.css" rel="stylesheet">
    <link href="css/jquery.simplecolorpicker.css" rel="stylesheet">
	
	<?php if (getenv('DEVELOPMENT_ENV')): ?>
		<link href="../!dev/common/css/map-elements.css" rel="stylesheet">
		<link href="../!dev/common/css/common.css" rel="stylesheet">
		<link href="../!dev/admin/css/admin.css" rel="stylesheet">
	<?php else: ?>
		<link href="../common/css/map-elements.css" rel="stylesheet">
	    <link href="../common/css/common.css" rel="stylesheet">
		<link href="css/admin.css" rel="stylesheet">
	<?php endif; ?>
	
	<!-- head js library  -->
    <script src="../common/js/head.min.js"></script>
  </head>

  <body>
	<div class="navbar navbar-fixed-top">
	  <div id="mainNavigation" class="navbar-inner hide">
		<div class="container" >    
		<a class="brand" href="#"><img alt="logo" src="../common/img/logo.png" /></a>
		
		<!-- navigation links -->
		<!-- some navigation li are hidden, they're only needed for bootstrap tab plugin -->
		<ul  class="nav">
			<li class='active hide'>
				<a href="#loginTab" data-action="tab">Login</a>
			</li>			
			<li>
				<a href="#mapsTab" data-action="tab"><i class="fam-map"></i> Maps</a>
			</li>		
			<li class="hide">
				<a href="#mapEditTab" ></a>
			</li>			
			<li class="hide">
				<a href="#mapViewTab" ></a>
			</li>
			
			<li class="divider-vertical"></li>
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                  <i class="fam-marker"></i> Map elements 
                  <b class="caret"></b>
                </a>
                <ul class="dropdown-menu" role="menu" >
                    <li><a tabindex="-1" href="#markersTab" data-action="tab">Markers</a></li>
                    <li class="hide"><a tabindex="-1" href="#markerEditTab" data-action="tab"></a></li>
                    <li><a tabindex="-1" href="#labelsTab" data-action="tab">Labels</a></li>
                    <li class="hide"><a tabindex="-1" href="#labelEditTab" data-action="tab"></a></li>
                    <li><a tabindex="-1" href="#markerTypesTab" data-action="tab">Marker types</a></li>
                    <li class="hide"><a tabindex="-1" href="#markerTypeEditTab" data-action="tab"></a></li>
                    <li><a tabindex="-1" href="#dictionariesTab" data-action="tab">Dictionaries</a></li>
                    <li class="hide"><a tabindex="-1" href="#dictionaryEditTab" data-action="tab"></a></li>
                </ul>
            </li>
            <li class="divider-vertical"></li>
			<li id="mediaDropdown" class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                  <i class="fam-photos"></i> Media
                  <b class="caret"></b>
                </a>
                <ul class="dropdown-menu" role="menu" >
                  <li><a tabindex="-1" href="#images">Images</a></li>
                  <li><a tabindex="-1" href="#icons">Icons</a></li>
                  <li><a tabindex="-1" href="#uploadImages">Upload images</a></li>
                  <li><a tabindex="-1" href="#uploadIcons">Upload icons</a></li>
                </ul>
            </li>
            <li class="divider-vertical"></li>
            <li id="toolsDropdown" class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                  <i class="fam-wrench"></i> Tools
                  <b class="caret"></b>
                </a>
                <ul class="dropdown-menu" role="menu" >
                    <li><a tabindex="-1" href="#settingsTab" data-action="tab">Settings</a></li>
                    <li><a tabindex="-1" href="#clearCache" >Clear cache</a></li>
                </ul>
            </li>
			<li class="divider-vertical"></li>
			<li>
				<a href="#passwordTab" data-action="tab"><i class="fam-lock"></i> Change password</a>
			</li>	
			
			<li class="hide">
				<a href="#regionsTab" >Regions</a>
			</li>
			<li class="hide">
				<a href="#regionEditTab" ></a>
			</li>
			
		 </ul>
		<ul class="nav pull-right">
			<li class="divider-vertical"></li>
			<li>
				<a href="#logoutTab" data-action="tab"><i class="fam-exit"></i> Logout</a>
			</li>
		</ul>
	  </div>
	  </div>
	</div>
	
	<!--tabs-->
	<div id="tabsContainer" class="tab-content hide"  >
	  <div id="loginTab" class="tab-pane fade in active" >
		<div class="form-panel" >
			<div class="panel-title" >YourMap - admin panel</div>
			<div class="panel-body">
				<form class="h-centered">
				    <fieldset>
				        <div class="control-group">
        				    <label class="control-label" >Password:</label>
        					<div class="controls">
        						<input type="password" name="pass" class="input-medium required" minlen="6" placeholder="password..." value="">
        					</div>
    					</div>
    					<button type="submit" class="btn btn-primary">Login</button>
				    </fieldset>
				</form>
			</div>
            <div class="version">v. 1.12.2</div>
		</div>
	  </div>
	
		<div id="mapsTab" class="tab-pane fade" >
		
			<div class="form-panel" >
				<div class="panel-title">Maps:</div>
				<div class="panel-body">
					<div class="btn-group">
						<a class="btn btn-small" href="#add"><i class="icon-plus"></i> Add</a>
						<a class="btn btn-small" href="#edit"><i class="icon-pencil"></i> Edit</a>
						<a class="btn btn-small" href="#upload"><i class="icon-upload"></i> Upload image</a>
						<a class="btn btn-small" href="#preview"><i class="icon-zoom-in"></i> Preview</a>
						<a class="btn btn-small" href="#remove"><i class="icon-trash"></i> Delete</a>
					</div>	
					
					<div class="btn-group">
					  <a class="btn btn-small dropdown-toggle" data-toggle="dropdown" href="#" >
					    Actions
					    <span class="caret"></span>
					  </a>
						<ul class="dropdown-menu" role="menu">
						  <li><a tabindex="-1" href="#moveElements">Move elements</a></li>
						  <li><a tabindex="-1" href="#addMarker">Add marker</a></li>
						  <li><a tabindex="-1" href="#addLabel">Add label</a></li>
						  <li><a tabindex="-1" href="#browseMarkers">Browse markers</a></li>
						  <li><a tabindex="-1" href="#browseLabels">Browse labels</a></li>
						  <li><a tabindex="-1" href="#editRegions">Edit Regions</a></li>
						</ul>
					</div>	
					
					<div class="tree-holder">
						<span data-state="noMaps">No maps.</span>	
					</div>	
							
					<div class="info-tip"><i class="icon-info-sign" ></i> You can drag and drop maps to change their parent or order.</div>
				</div>
			</div>
		</div>
		
		<div id="mapEditTab" class="tab-pane fade" >

			<div class="form-panel" >
				<div class="panel-title" data-state="new">New map:</div>
				<div class="panel-title" data-state="edit">Edit map:</div>
				 <form class="form-horizontal">
					<fieldset>
				    <div class="panel-body">
					<div class="alert alert-error hide"></div>
					
					    <div class="control-group" data-state="edit">
                            <label class="control-label" >Id:</label>
                            <div class="controls">
                              <input type="hidden" name="id"  />
                              <input type="text" name="id" class="input-mini"  disabled />
                            </div>
                        </div>  
						 
						<div class="control-group">
							<label class="control-label" for="metNameInput">Name: <img src="img/required.png" alt="required" /></label>
							<div class="controls">
							  <input id="metNameInput" name="name" type="text" class="required" minlen="2" maxlen="30" placeholder="type..." >
							</div>
						</div>		
					
						<div class="control-group">
							<label class="control-label" for="mapetEnabledCbx">Enabled:</label>
							<div class="controls">
								<input id="mapetEnabledCbx" name="enabled" type="checkbox">
							</div>
						</div>					

						<div class="control-group">
							<label class="control-label" for="metLegendCbx">Show legend:</label>
							<div class="controls">
								<input id="metLegendCbx" name="showLegend" type="checkbox">
							</div>
						</div>					

						<div class="control-group">
							<label class="control-label" for="metZoomSelect">Initial zoom: </label>
							<div id="metZoomSelect"  class="controls">
								<select name="zoom" >
								  <option value="default">default</option>
								  <option value="6">1%</option>
								  <option value="5">3%</option>
								  <option value="4">6%</option>
								  <option value="3">12%</option>
								  <option value="2">25%</option>
								  <option value="1">50%</option>
								  <option value="0">100%</option>
								</select>
							</div>
						</div>						

    				</div>

					<div class="panel-footer">
						<button type="submit" class="btn btn-primary">Save</button>
						<button type="button" class="btn" data-action="cancel">Cancel</button>
					</div>						
											
					</fieldset>
				</form>
			</div>
			
		</div>

		<div id="mapViewTab" class="tab-pane fade" >
		
		  <div class="left-column">
		       <div class="btn-group" data-state="marker label">
                 <a class="btn btn-small" href="#editSelected"><i class="icon-pencil"></i> Edit selected</a>
                 <a class="btn btn-small" href="#removeSelected"><i class="icon-trash"></i> Delete selected</a>
               </div>  
                <h5 data-state="marker label" >Changed positions:</h5>
                <div class="status" data-action="status" ></div>
    
                <div data-state="region" class="region-cbx">
                    <label class="control-label" for="mvtCbx" > Remember current zoom:</label>
                    <input id="mvtCbx" name="region-zoom" type="checkbox" >
                </div>
                
                <div class="buttons">
                    <button type="button" class="btn btn-primary" data-action="save">Save changes</button>
                    <button type="button" class="btn" data-action="cancel" data-state="region">Cancel</button>
                </div>
                
               <div class="info-tip" data-state="marker label" ><i class="icon-info-sign" ></i> To move marker or label click on it once to select it and next drag it to desired position.</div>
               <div class="info-tip" data-state="region"><i class="icon-info-sign" ></i> You're in region Edit mode. Move the map to desired position.</div>

			    <div data-state="marker label" class="marker-info-cbx" >
                    <label class="control-label" for="mvtMpCbx" >Turn off marker info popups:</label>
                    <input id="mvtMpCbx" type="checkbox" >
                </div>
                


		  </div>
		
		  <div class="right-column">
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
                
                <div class="crosshair" data-state="region"></div>
                  
             </div> 
		  </div>
			
		</div>
		
		

		<div id="markersTab" class="tab-pane fade"  >

			<div class="form-panel" >
				<div class="panel-title">Markers:</div>
				<div class="panel-body">
					<div class="btn-group">
						<a class="btn btn-small" href="#add"><i class="icon-plus"></i> Add</a>
						<a class="btn btn-small" href="#edit"><i class="icon-pencil"></i> Edit</a>
						<a class="btn btn-small" href="#move"><i class="icon-move"></i> Move on map</a>
						<a class="btn btn-small" href="#remove"><i class="icon-trash"></i> Delete</a>
					</div>
                    <div class="pull-right">
                        
                        <form class="form-search pull-right"> 
                          <input type="text" class="input-medium" placeholder="filter by title...">
                          <button type="submit" class="btn btn-small btn-primary" ><i class="icon-search"></i> Filter</button>
                        </form>
                        
                        <div class="pull-right" style="clear:both;">
                            <input type="hidden" 
                                class="required"
                                name="map" 
                                data-property-Name="id" 
                                data-label=".input-label"
                                data-label-Falsey-Val="none"
                                data-label-Attr="html" 
                                />
                            <span class="input-label" ></span>
                            <button class="btn btn-small" type="button" data-action="select-map">Select map...</button>
                            <button class="btn btn-small" type="button" data-action="clear-map">Clear</button>
                            <i class="info-icon icon-question-sign" rel="tooltip" title="Map to filter"></i>
                        </div>

                    </div>
					<div style="clear:both;"></div>
					
					<table id="mtTable" class="table table-condensed table-bordered table-hover table-striped"  >				
						<thead>
						  <tr>
							<th class="id-column">Id:</th>
							<th>Title:</th>
							<th>Type:</th>
							<th>Map:</th>
							<th class="narrow-column">Enabled:</th>
						  </tr>
						</thead>
						
						<tbody>
						</tbody>
					</table>
				</div>
			</div>
			
		</div>		
	  
		<div id="markerEditTab" class="tab-pane fade" >

			<div class="form-panel" >
				<div class="panel-title" data-state="new">New marker:</div>
				<div class="panel-title" data-state="edit">Edit marker details:</div>
				<form class="form-horizontal">
				  <fieldset>
				<div class="panel-body">
					<div class="alert alert-error hide"></div>
					
                        <div class="control-group" data-state="edit">
                            <label class="control-label" >Id:</label>
                            <div class="controls">
                              <input type="hidden" name="id"  />
                              <input type="text" name="id" class="input-mini"  disabled />
                            </div>
                        </div>  
					
						<div class="control-group">
							<label class="control-label" for="metNameText">Title: <img src="img/required.png" alt="required" /></label>
							<div class="controls">
							  <input id="metNameText" name="title" type="text" class="input-xlarge required" minlen="2" maxlen="60" placeholder="type..." >
							</div>
						</div>	
						
						<div class="control-group">
							<label class="control-label" for="metTypeSelect">Marker type:</label>
							<div class="controls">
								<select id="metTypeSelect" name="markerTypeId" class="required">
								</select>
							</div>
						</div>		
						
						<div class="control-group">
							<label class="control-label" for="metEnabledCbx">Enabled:</label>
							<div class="controls">
								<input id="metEnabledCbx" name="enabled" type="checkbox" >
							</div>
						</div>	
						
						<div class="control-group">
							<label class="control-label">Map: <img src="img/required.png" alt="required" /></label>
							<div class="controls">
								<input type="hidden" 
									class="required"
									name="map" 
									data-property-Name="id" 
									data-label=".input-label"
									data-label-Falsey-Val="none"
									data-label-Attr="html" 
									/>
								<span class="input-label" >none</span>
								<button class="btn btn-small" type="button" data-action="select-map">Select...</button>
							</div>
						</div>	
						
						<div class="control-group">
							<label class="control-label">Region: </label>
							<div class="controls">
								<input type="hidden" 
									name="region" 
									data-property-Name="id" 
									data-label=".input-label"
									data-label-Falsey-Val="none"
									data-label-Attr="html" 
									data-label-Template="{name}"
									/>
								<span class="input-label" >none</span>
								<button class="btn btn-small" type="button" data-action="select-region">Select...</button>
								<button  class="btn btn-small" type="button" data-action="clear-region">Clear</button>
								<i class="info-icon icon-question-sign" rel="tooltip" title="Region that the marker will be assigned to. Used only when displaying map breadcrumb in the search results."></i>
							</div>
						</div>	

						<div class="control-group">
							<label class="control-label" for="metXInput">x: <img src="img/required.png" alt="required" /></label>
							<div class="controls">
							  <input id="metXInput" name="x" type="text" class="input-mini required" integer="0" value="0" >
							  <i class="info-icon icon-question-sign" rel="tooltip" title="x coordinate on a map"></i>
							</div>
						</div>							
						
						<div class="control-group">
							<label class="control-label" for="metYInput">y: <img src="img/required.png" alt="required" /></label>
							<div class="controls">
								<input id="metYInput" name="y" type="text" class="input-mini required" integer="0" value="0"  >
								<i class="info-icon icon-question-sign" rel="tooltip" title="y coordinate on a map"></i>
							</div>
						</div>							
						
						<div class="control-group">
							<label class="control-label" >Icon: </label>
							<div class="controls">
								<input type="hidden" 
									name="icon" 
									data-property-Name="id" 
									data-label=".input-label"
									data-label-Falsey-Val="../common/img/blank.gif"
									data-label-Attr="src" 
									data-label-Template="{url}"
									/>
								<img class="input-label" src="../common/img/blank.gif" >
								<button id="metBrowseIconBtn"  class="btn btn-small"  type="button">Change...</button>
								<button id="metDefaultIconBtn"  class="btn btn-small"  type="button">Default</button>
								<i class="info-icon icon-question-sign" rel="tooltip" title="Icon for this marker. Defined in the marker type by default."></i>
							</div>
						</div>
						
						<div class="control-group">
							<label class="control-label" >Image: </label>
							<div class="controls">
								<input type="hidden" 
									name="image" 
									data-property-Name="id" 
									data-label=".input-label"
									data-label-Falsey-Val="../common/img/blank.gif"
									data-label-Attr="src" 
									data-label-Template="{url}"
									/>
								<img class="input-label" src="../common/img/blank.gif" >
								<button id="metBrowseImageBtn"  class="btn btn-small"  type="button">Change...</button>
								<button id="metClearImageBtn"  class="btn btn-small"  type="button">Clear</button>
								<i class="info-icon icon-question-sign" rel="tooltip" title="Image displayed in marker info popup."></i>
							</div>
						</div>	
						
						<div id="metParamInputs">
                        </div>	
    				</div>

					<div class="panel-footer">
						<button type="submit" class="btn btn-primary">Save</button>
						<button type="button" class="btn" data-action="save-and-position">Save and position</button>
						<button type="button" class="btn" data-action="cancel">Cancel</button>
					</div>							
				  </fieldset>
				</form>
			</div>
				
		</div>
		
		<div id="markerTypesTab" class="tab-pane fade"  >

			<div class="form-panel" >
				<div class="panel-title">Marker types:</div>
				<div class="panel-body">
					<div class="btn-group">
						<a class="btn btn-small" href="#add"><i class="icon-plus"></i> Add</a>
						<a class="btn btn-small" href="#edit"><i class="icon-pencil"></i> Edit</a>
						<a class="btn btn-small" href="#remove"><i class="icon-trash"></i> Delete</a>
					</div>
							
					<table class="table table-bordered table-condensed table-hover table-striped">
					  <thead>
						<tr>
						  <th class="id-column">id:</th>
						  <th>Type:</th>
						</tr>
					  </thead>
					  <tbody>
					  </tbody>
					</table>
				</div>
			</div>
		</div>
		  
		<div id="markerTypeEditTab" class="tab-pane fade"  >
			<div class="form-panel" >
			<div class="panel-title" data-state="new">New marker type:</div>
			<div class="panel-title" data-state="edit">Edit marker type:</div>
			<form class="form-horizontal">
			  <fieldset>
			<div class="panel-body">
				<div class="alert alert-error hide"></div>
				
                <div class="control-group" data-state="edit">
                    <label class="control-label" >Id:</label>
                    <div class="controls">
                      <input type="hidden" name="id"  />
                      <input type="text" name="id" class="input-mini"  disabled />
                    </div>
                </div>  
				
				<div class="control-group">
					<label class="control-label" for="mteNameInput">Name: <img src="img/required.png" alt="required" /></label>
					<div class="controls">
					  <input id="mteNameInput" name="name" type="text" class="required" minlen="2" maxlen="30"  placeholder="type..." >
					</div>
				</div>					
				<div class="control-group">
					<label class="control-label" for="mteCssNameInput">Css name: <img src="img/required.png" alt="required" /></label>
					<div class="controls">
						<input id="mteCssNameInput" name="cssName" type="text" class="required"  minlen="2" maxlen="30" cssname="true"  placeholder="type..." >
						<i class="info-icon icon-question-sign" rel="tooltip" title="Used to generate dinamically css file with marker type colors."></i>
					</div>
				</div>		
				<div class="control-group">
					<label class="control-label" >Marker color: </label>
					<div class="controls">
						<select name="markerColor" rel="colorpicker" style="display: none;">
							<option value="#ac725e">#ac725e</option>
							 <option value="#d06b64">#d06b64</option>
							 <option value="#f83a22">#f83a22</option>
							 <option value="#fa573c">#fa573c</option>
							 <option value="#ff7537">#ff7537</option>
							 <option value="#ffad46">#ffad46</option>
							  <option value="#42d692">#42d692</option>
							  <option value="#16a765">#16a765</option>
							  <option value="#7bd148">#7bd148</option>
							  <option value="#7bd148">#7bd148</option>
							  <option value="#7bd148">#7bd148</option>
							  <option value="#fad165">#fad165</option>
							  <option value="#92e1c0">#92e1c0</option>
							  <option value="#9fe1e7">#9fe1e7</option>
							  <option value="#9fc6e7">#9fc6e7</option>
							  <option value="#4986e7">#4986e7</option>
							  <option value="#9a9cff">#9a9cff</option>
							  <option value="#b99aff">#b99aff</option>
							  <option value="#c2c2c2">#c2c2c2</option>
							  <option value="#cabdbf">#cabdbf</option>
							  <option value="#cca6ac">#cca6ac</option>
							  <option value="#f691b2">#f691b2</option>
							  <option value="#cd74e6">#cd74e6</option>
							  <option value="#a47ae2">#a47ae2</option>
						</select>
						<i class="info-icon icon-question-sign" rel="tooltip" title="Marker color displayed on a map when zoom is below 50%."></i>
					</div>
				</div>					
				<div class="control-group">
					<label class="control-label">Marker hovered color: </label>
					<div class="controls">
						<select name="markerHoveredColor" rel="colorpicker" style="display: none;">
							<option value="#ac725e">#ac725e</option>
							 <option value="#d06b64">#d06b64</option>
							 <option value="#f83a22">#f83a22</option>
							 <option value="#fa573c">#fa573c</option>
							 <option value="#ff7537">#ff7537</option>
							 <option value="#ffad46">#ffad46</option>
							  <option value="#42d692">#42d692</option>
							  <option value="#16a765">#16a765</option>
							  <option value="#7bd148">#7bd148</option>
							  <option value="#7bd148">#7bd148</option>
							  <option value="#7bd148">#7bd148</option>
							  <option value="#fad165">#fad165</option>
							  <option value="#92e1c0">#92e1c0</option>
							  <option value="#9fe1e7">#9fe1e7</option>
							  <option value="#9fc6e7">#9fc6e7</option>
							  <option value="#4986e7">#4986e7</option>
							  <option value="#9a9cff">#9a9cff</option>
							  <option value="#b99aff">#b99aff</option>
							  <option value="#c2c2c2">#c2c2c2</option>
							  <option value="#cabdbf">#cabdbf</option>
							  <option value="#cca6ac">#cca6ac</option>
							  <option value="#f691b2">#f691b2</option>
							  <option value="#cd74e6">#cd74e6</option>
							  <option value="#a47ae2">#a47ae2</option>
						</select>
						<i class="info-icon icon-question-sign" rel="tooltip" title="Marker color displayed on map when marked is selected."></i>
					</div>
				</div>				
				<div class="control-group">
					<label class="control-label" for="mteShowLegendCbx">Show on legend:</label>
					<div class="controls">
						<input id="mteShowLegendCbx" name="showOnLegend" type="checkbox">
					</div>
				</div>			
				
				<div class="control-group">
					<label class="control-label" >Default icon: <img src="img/required.png" alt="required" /></label>
					<div class="controls">
						<input type="hidden" 
    							class="required"
    							name="defaultIcon" 
    							data-property-Name="id" 
    							data-label=".input-label"
    							data-label-Falsey-Val="../common/img/blank.gif"
    							data-label-Attr="src" 
    							data-label-Template="{url}"
							/>
						<img class="input-label" src="../common/img/blank.gif" >
						<button id="mteBrowseIconBtn" class="btn btn-small"  type="button">Change...</button>
						<button id="mteClearIconBtn" class="btn btn-small"  type="button">Clear</button>
						<i class="info-icon icon-question-sign" rel="tooltip" title="Default icon, can be overwritten by setting different icon on each marker."></i>
					</div>
				</div>			
				
				<div class="control-group">
					<label >Parameters:</label>
					<div class="btn-group">
					  <a class="btn btn-small" href="#edit"><i class="icon-pencil"></i> Edit</a>
					  <a class="btn btn-small" href="#moveUp"><i class="icon-arrow-up"></i> Move up</a>
					  <a class="btn btn-small" href="#moveDown"><i class="icon-arrow-down"></i> Move down</a>
					</div>
					<table class="table table-bordered table-condensed table-hover">
						<thead>
							<tr>
								<th class="h-centered v-centered" style="width: 110px;">Param type:
								  <i class="info-icon icon-question-sign" rel="tooltip" title="Parameter types: &lt;br/&gt;- text (max. 80 chars)&lt;br/&gt;- long text (max. 65000 chars, all html tags are supported, it will be displayed as link that opens popup window with parameter value content)&lt;br/&gt;- dictionary (predefined values)&lt;br/&gt;- link (clickable link that will open specified URL)."></i>
								</th>
								<th class="h-centered v-centered ">Label:
									<i class="info-icon icon-question-sign" rel="tooltip" title="This label will be displayed on:<br/>&#x2022; marker edition form<br/>&#x2022; marker info box in front of the parameter value<br/>If parameter type is &quot;Long text&quot;: it will be displayed as a link that will show popup window with parameter value."></i>
								</th>
								<th class="h-centered v-centered narrow-column">Enabled:</th>
								<th class="h-centered v-centered narrow-column">Searchable:</th>
								<th class="h-centered v-centered narrow-column">Show label:
								    <i class="info-icon icon-question-sign" rel="tooltip" title="Will the label be displayed next to the parameter value."></i>
								</th>
								<th class="h-centered v-centered narrow-column">Visible:
								    <i class="info-icon icon-question-sign" rel="tooltip" title="Will the parameter be visible at 100% zoom and in the search results."></i>
								</th>
							</tr>
						</thead>
						<tbody>

						</tbody>
					</table>
				 </div>
    			</div>
    			
				<div class="panel-footer">
					<button type="submit" class="btn btn-primary" >Save</button>
					<button type="button" class="btn" data-action="cancel">Cancel</button>
				</div>	
			  </fieldset>
			</form>

		</div>
		</div>	  
		
        <div id="dictionariesTab" class="tab-pane fade"  >
            <div class="form-panel" >
                <div class="panel-title">Dictionaries:</div>
                <div class="panel-body">
                    <div class="btn-group">
                        <a class="btn btn-small" href="#add"><i class="icon-plus"></i> Add</a>
                        <a class="btn btn-small" href="#edit"><i class="icon-pencil"></i> Edit</a>
                        <a class="btn btn-small" href="#remove"><i class="icon-trash"></i> Delete</a>
                    </div>
                            
                    <table class="table table-bordered table-condensed table-hover table-striped">
                      <thead>
                        <tr>
                          <th class="id-column">id:</th>
                          <th>Name:</th>
                        </tr>
                      </thead>
                      <tbody>
                      </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div id="dictionaryEditTab" class="tab-pane fade"  >

            <div class="form-panel" >
                <div class="panel-title" data-state="new">
                    New dictionary:
                </div>
                <div class="panel-title" data-state="edit">
                    Edit dictionary:
                </div>
                <div class="panel-body">
                <form class="form-horizontal">
                    <fieldset>
                        <div class="alert alert-error hide"></div>
                        <input type="hidden" name="id" >
                        <div class="control-group ">
                            <label class="control-label" for="detNameInput">Name: <img src="img/required.png" alt="required" /></label>
                            <div class="controls">
                                <input id="detNameInput" name="name" type="text" class="input-xlarge required"
                                minlen="2" maxlen="60" placeholder="type...">
                                <i class="info-icon icon-question-sign" rel="tooltip" title="name of Dictionary"></i>
                            </div>
                        </div>
                    </fieldset>
                </form>

                    <span class="nav-header">Dictionary values:</span>
    
                    <form id="detEntryForm" class="form-horizontal">
                        <fieldset>
                            <div class="control-group ">
                                <label class="control-label" for="detEntryNameInput" >Entry value: </label>
                                <div class="controls">
                                    <input id="detEntryNameInput" type="text" class="input-medium required"
                                    minlen="2" maxlen="80" placeholder="type...">
                                    <button type="button" class="btn btn-small" data-action="save" >
                                        <i class="icon-ok-sign"></i> Save
                                    </button>
                                    <button type="button" class="btn btn-small" data-action="add" >
                                        <i class="icon-plus"></i> Add new
                                    </button>
                                    <div class="btn-group">
                                        <a class="btn btn-small" href="#remove"><i class="icon-trash"></i> Delete selected</a>
                                    </div>
                                    <i class="info-icon icon-question-sign" rel="tooltip" title="Add or edit value to dictionary."></i>
                                </div>
                            </div>
                        </fieldset>
                    </form>

                    <table class="table table-bordered table-condensed table-hover table-striped">
                        <thead>
                            <tr>
                                <th class="id-column">id:</th>
                                <th>Value:</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>

                </div>

                <div class="panel-footer">
                    <button type="button" class="btn btn-primary" data-action="save">Save</button>
                    <button type="button" class="btn" data-action="cancel">Cancel</button>
                </div>
            </div>
        </div>
		
		<div id="labelsTab" class="tab-pane fade"  >

			<div class="form-panel" >
				<div class="panel-title">Labels:</div>
				<div class="panel-body">
					<div class="btn-group">
						<a class="btn btn-small" href="#add"><i class="icon-plus"></i> Add</a>
						<a class="btn btn-small" href="#edit"><i class="icon-pencil"></i> Edit</a>
						<a class="btn btn-small" href="#move"><i class="icon-move"></i> Move on map</a>
						<a class="btn btn-small" href="#remove"><i class="icon-trash"></i> Delete</a>
					</div>
                    <div class="pull-right">
                        
                        <form class="form-search pull-right"> 
                          <input type="text" class="input-medium" placeholder="filter by title...">
                          <button type="submit" class="btn btn-small btn-primary" ><i class="icon-search"></i> Filter</button>
                        </form>
                        
                        <div class="pull-right"  style="clear:both;">
                            <input type="hidden" 
                                class="required"
                                name="map" 
                                data-property-Name="id" 
                                data-label=".input-label"
                                data-label-Falsey-Val="none"
                                data-label-Attr="html" 
                                />
                            <span class="input-label" ></span>
                            <button class="btn btn-small" type="button" data-action="select-map">Select map...</button>
                            <button class="btn btn-small" type="button" data-action="clear-map">Clear</button>
                            <i class="info-icon icon-question-sign" rel="tooltip" title="Map to filter"></i>
                        </div>
                    </div>
                     <div style="clear:both;"></div>
					
					<table id="ltTable" class="table table-condensed table-bordered"  >
										
						<thead>
						  <tr>
							<th class="id-column">Id:</th>
							<th>Text or icon:</th>
							<th>Type:</th>
							<th>Map:</th>
							<th class="narrow-column">Enabled:</th>
						  </tr>
						</thead>
						
						<tbody>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		
		<div id="regionsTab" class="tab-pane fade"  >

			<div class="form-panel" >
				<div class="panel-title"></div>
				<div class="panel-body">
					<div class="btn-group">
						<a class="btn btn-small" href="#add"><i class="icon-plus"></i> Add</a>
						<a class="btn btn-small" href="#edit"><i class="icon-pencil"></i> Edit</a>
						<a class="btn btn-small" href="#move"><i class="icon-move"></i> Move on map</a>
						<a class="btn btn-small" href="#remove"><i class="icon-trash"></i> Delete</a>
					</div>
					
					<table  class="table table-condensed table-bordered"  >
										
						<thead>
						  <tr>
							<th class="id-column">Id:</th>
							<th>Name:</th>
						  </tr>
						</thead>
						
						<tbody>  
						</tbody>
					</table>
                    
					
				</div>

			</div>
			
		</div>
		
		<div id="regionEditTab" class="tab-pane fade" >
			<div class="form-panel" >
				<div class="panel-title" data-state="new">New region:</div>
				<div class="panel-title" data-state="edit">Edit region:</div>
				<form class="form-horizontal">
				  <fieldset>
				     <div class="panel-body">
					    <div class="alert alert-error hide"></div>
						
                        <div class="control-group" data-state="edit">
                            <label class="control-label" >Id:</label>
                            <div class="controls">
                              <input type="hidden" name="id"  />
                              <input type="text" name="id" class="input-mini"  disabled />
                            </div>
                        </div>  
						
						<div class="control-group ">
							<label class="control-label" for="retNameInput">Name: <img src="img/required.png" alt="required" /></label>
							<div class="controls">
								<input id="retNameInput" name="name" type="text" class="input-xlarge required" 
									minlen="2" maxlen="20" placeholder="type...">
								<i class="info-icon icon-question-sign" rel="tooltip" title="name of region"></i>
							</div>
						</div>
						
                        <div class="control-group">
                            <label class="control-label" for="retXInput">x: <img src="img/required.png" alt="required" /></label>
                            <div class="controls">
                              <input id="retXInput" name="x" type="text" class="input-mini required" integer="0" value="0" >
                              <i class="info-icon icon-question-sign" rel="tooltip" title="x coordinate on a map"></i>
                            </div>
                        </div>                          
                        
                        <div class="control-group">
                            <label class="control-label" for="retYInput">y: <img src="img/required.png" alt="required" /></label>
                            <div class="controls">
                                <input id="retYInput" name="y" type="text" class="input-mini required" integer="0" value="0"  >
                                <i class="info-icon icon-question-sign" rel="tooltip" title="y coordinate on a map"></i>
                            </div>
                        </div>  
						
						<div class="control-group">
                            <label class="control-label" for="retZoomSelect">Zoom: </label>
                            <div id="retZoomSelect"  class="controls">
								<select name="zoom" >
								  <option value="default">no change</option>
								  <option value="6">1%</option>
								  <option value="5">3%</option>
								  <option value="4">6%</option>
								  <option value="3">12%</option>
								  <option value="2">25%</option>
								  <option value="1">50%</option>
								  <option value="0">100%</option>
								</select>
                                <i class="info-icon icon-question-sign" rel="tooltip" title="all or computer or employee?"></i>
                            </div>
                        </div>  
        			</div>
						
						<div class="panel-footer">
                            <button type="submit" class="btn btn-primary">Save</button>
                            <button type="button" class="btn" data-action="save-and-position">Save and position region</button>
                            <button type="button" class="btn" data-action="cancel">Cancel</button>
                        </div>
                        
						</fieldset>
					</form>
			</div>
		</div>
		
		<div id="labelEditTab" class="tab-pane fade" >
			<div class="form-panel" >
				<div class="panel-title" data-state="new">New label:</div>
				<div class="panel-title" data-state="edit">Edit label:</div>
				<form class="form-horizontal">
				  <fieldset>
					<div class="panel-body">
					<div class="alert alert-error hide"></div>
					      
                        <div class="control-group" data-state="edit">
                            <label class="control-label" >Id:</label>
                            <div class="controls">
                              <input type="hidden" name="id"  />
                              <input type="text" name="id" class="input-mini"  disabled />
                            </div>
                        </div>  
					      
						<div class="control-group">
							<label class="control-label" >Type: </label>
							<div class="controls">
								<label class="radio inline">
								  <input type="radio" name="type" value="text" checked  />
								  Text
								</label>
								<label class="radio inline">
								  <input type="radio" name="type" value="icon" />
								  Icon
								</label>
							</div>
						</div>
						
						<div class="control-group">
                            <label class="control-label" for="letEnabledCbx">Enabled:</label>
                            <div class="controls">
                                <input id="letEnabledCbx" name="enabled" type="checkbox">
                            </div>
                        </div>  
                        
                        <div class="control-group">
							<label class="control-label">Map: <img src="img/required.png" alt="required" /></label>
							<div class="controls">
								<input type="hidden" 
									class="required"
									name="map" 
									data-property-Name="id" 
									data-label=".input-label"
									data-label-Falsey-Val="none"
									data-label-Attr="html" 
									/>
								<span class="input-label" >none</span>
								<button class="btn btn-small" type="button" data-action="select-map">Select...</button>
							</div>
						</div>	
						
						<div id="letIconTypeInputControl" class="control-group hide">
							<label class="control-label">Icon: <img src="img/required.png" alt="required" /></label>
                            <div class="controls">
                                <input type="hidden" class="required"
                                    name="icon" 
                                    data-property-Name="id" 
                                    data-label=".input-label"
                                    data-label-Falsey-Val="../common/img/blank.gif"
                                    data-label-Attr="src" 
                                    data-label-Template="{url}"
                                    />
                                <img class="input-label" src="../common/img/blank.gif" >
                                <button id="letBrowseIconBtn"  class="btn btn-small"  type="button">Change...</button>
                            </div>
						</div>
						
						<div id="letTextTypeInputControl" class="control-group hide">
                            <label class="control-label" for="letTextInput">Text: <img src="img/required.png" alt="required" /></label>
                            <div class="controls">
                            	<textarea name="text" class="input-xxlarge required" rows="2" minlen="2" maxlen="160"></textarea>
                                <i class="info-icon icon-question-sign" rel="tooltip" title="Max 160 chars. line breaks are preserved."></i>
                            </div>
                        </div>
						
                        <div class="control-group">
                            <label class="control-label" for="letXInput">x: <img src="img/required.png" alt="required" /></label>
                            <div class="controls">
                              <input id="letXInput" name="x" type="text" class="input-mini required" integer="0" value="0" >
                              <i class="info-icon icon-question-sign" rel="tooltip" title="x coordinate on a map"></i>
                            </div>
                        </div>                          
                        
                        <div class="control-group">
                            <label class="control-label" for="letYInput">y: <img src="img/required.png" alt="required" /></label>
                            <div class="controls">
                                <input id="letYInput" name="y" type="text" class="input-mini required" integer="0" value="0"  >
                                <i class="info-icon icon-question-sign" rel="tooltip" title="y coordinate on a map"></i>
                            </div>
                        </div>  
						
						
						<div class="control-group">
							<label class="control-label" for="nameText">Link to map: </label>
							<div class="controls">
								<input type="hidden" 
									name="linkMap" 
									data-property-Name="id" 
									data-label=".input-label"
									data-label-Falsey-Val="none"
									data-label-Attr="html" 
									/>
								<span class="input-label" >none</span>
								<button class="btn btn-small" type="button" data-action="select-link-map">Select...</button>
								<button  class="btn btn-small" type="button" data-action="clear-link-map">Clear</button>
								<i class="info-icon icon-question-sign" rel="tooltip" title="Is the label linked to specific map."></i>
							</div>
						</div>		
						
					    <div class="control-group">
                            <label class="control-label">Link to region: </label>
                            <div class="controls">
                                <input type="hidden" 
                                    name="linkRegion" 
                                    data-property-Name="id" 
                                    data-label=".input-label"
                                    data-label-Falsey-Val="none"
                                    data-label-Attr="html" 
                                    />
                                <span class="input-label" >none</span>
                                <button class="btn btn-small" type="button" data-action="select-region">Select...</button>
                                <button  class="btn btn-small" type="button" data-action="clear-region">Clear</button>
                                <i class="info-icon icon-question-sign" rel="tooltip" title="Link to specific region in the selected map."></i>
                            </div>
                        </div>  
            			</div>
						
                        <div class="panel-footer">
                            <button type="submit" class="btn btn-primary">Save</button>
                            <button type="button" class="btn" data-action="save-and-position">Save and position</button>
                            <button type="button" class="btn" data-action="cancel">Cancel</button>
                        </div>  
						</fieldset>
					</form>
			</div>
		</div>

		<div id="settingsTab" class="tab-pane fade"  >
			<div class="form-panel" >
				<div class="panel-title">Application settings:</div>
				<form id="settingsForm" class="form-horizontal">
				  <fieldset>
				    <div class="panel-body">
						<div class="control-group">
							<label class="control-label" for="stPanelOpenedCbx">Left panel opened at startup:</label>
							<div class="controls">
								<input id="stPanelOpenedCbx" name="panelOpened" type="checkbox">
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for="stDisableViewTabCbx">Disable view tab:</label>
							<div class="controls">
								<input id="stDisableViewTabCbx" name="disableViewTab" type="checkbox">
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for="stDefaultMarkerSelect">Default searching marker type selection:</label>
							<div class="controls">
								<select id="stDefaultMarkerSelect" name="defaultMarkerType">
								</select>
							</div>
						</div>	
				    </div>
                    <div class="panel-footer">
						<button type="submit" class="btn btn-primary">Save</button>
                    </div>  
					</fieldset>
				</form>
			</div>
		</div>
		
		<div id="passwordTab" class="tab-pane fade"  >
			<div class="form-panel" >
				<div class="panel-title">Change password:</div>
				<form id="passwordForm" class="form-horizontal">
				  <fieldset>
    				<div class="panel-body">
    					<div class="alert alert-error hide"></div>
    						<div class="control-group">
    							<label class="control-label" for="ptPasswordInput">Current password: <img src="img/required.png" alt="required" /></label>
    							<div class="controls">
    								<input id="ptPasswordInput" name="oldPassword" type="password"  class="required" pass="true" placeholder="type...">
    							</div>
    						</div>
    						<div class="control-group">
    							<label class="control-label" for="ptNewPassword1Input">New password: <img src="img/required.png" alt="required" /></label>
    							<div class="controls">
    								<input id="ptNewPassword1Input" name="newPassword1" type="password" class="required" pass="true"  placeholder="type...">
    							</div>
    						</div>
    						<div class="control-group">
    							<label class="control-label" for="ptnewPassword2Input">Retype new password: <img src="img/required.png" alt="required" /></label>
    							<div class="controls">
    								<input id="ptnewPassword2Input" name="newPassword2" type="password"  class="required" equalTo="#ptNewPassword1Input" placeholder="type...">
    							</div>
    						</div>	
            		</div>
								
                    <div class="panel-footer">
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>  
				  </fieldset>
				</form>
			</div>
		</div>
		
	  <div id="logoutTab" class="tab-pane fade" >
		<div class="form-panel" >
			<div class="panel-title" >You have logged out of the application.</div>
			<div class="panel-body">
				<button id="ltLoginAgain" type="button" class="btn">Login again</button>
			</div>
		</div>
	  </div>
	  
	</div>
	
	<div id="paramEditModal" class="modal hide fade" data-backdrop="static" >
		<form class="form-horizontal">
		  <div class="modal-header">
			<h3>Edit parameter:</h3>
		  </div>
		  <div class="modal-body">
				<div class="alert alert-error hide"></div>
			  <fieldset>
			  
				<input type="hidden" name="number" />
				
				<div class="control-group">
					<label class="control-label" for="pemTypeSelect">Type:</label>
					<div class="controls">
						<select id="pemTypeSelect" name="type">
						  <option value="text">Text</option>
						  <option value="longText">Long text</option>
						  <option value="dictionary">Dictionary</option>
						  <option value="link">Link</option>
						</select>
						<i class="info-icon icon-question-sign" rel="tooltip" title="Parameter types: &lt;br/&gt;- text (max. 80 chars)&lt;br/&gt;- long text (max. 65000 chars, all html tags are supported, it will be displayed as link that opens popup window with parameter value content)&lt;br/&gt;- dictionary (predefined values)&lt;br/&gt;- link (clickable link that will open specified URL)."></i>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="pemLabelInput">Label: <img src="img/required.png" alt="required" /></label>
					<div class="controls">
						<input id="pemLabelInput" name="label" type="text" class="required" minlen="2" maxlen="30" placeholder="type...">
						<i class="info-icon icon-question-sign" rel="tooltip" title="This label will be displayed on:<br/>&#x2022; marker edition form<br/>&#x2022; marker info box in front of the parameter value<br/>If parameter type is &quot;Long text&quot;: it will be displayed as a link that will show popup window with parameter value."></i>
					</div>
				</div>		
				
				<div id="pemDictionaryControlGroup" class="control-group hide">
					<label class="control-label" for="pemDictionarySelect">Dictionary:</label>
					<div class="controls">
						<select id="pemDictionarySelect" name="typeValue" class="required">
						</select>
						<i class="info-icon icon-question-sign" rel="tooltip" title="Select dictionary"></i>
					</div>
				</div>		
				
				<div class="control-group">
					<label class="control-label" for="pemEnabledCbx">Enabled:</label>
					<div class="controls">
						<input id="pemEnabledCbx" name="enabled" type="checkbox">
					</div>
				</div>	
				
				<div class="control-group">
					<label class="control-label" for="pemSearchableCbx">Searchable:</label>
					<div class="controls">
						<input id="pemSearchableCbx" name="searchable" type="checkbox">
					</div>
				</div>	
				
				<div class="control-group">
					<label class="control-label" for="pemShowLabelCbx">Show label:</label>
					<div class="controls">
						<input id="pemShowLabelCbx" name="showLabel" type="checkbox">
						<i class="info-icon icon-question-sign" rel="tooltip" data-placement="right" title="Will the label be displayed next to the parameter value."></i>
					</div>
				</div>				
				
				<div class="control-group">
					<label class="control-label" for="pemAlwaysVisibleCbx">Visible:</label>
					<div class="controls">
						<input id="pemAlwaysVisibleCbx" name="alwaysVisible"  type="checkbox">
						<i class="info-icon icon-question-sign" rel="tooltip" data-placement="right" title="Will the parameter be visible at 100% zoom and in the search results."></i>
					</div>
				</div>	
				</fieldset>
			
		  </div>
			<div class="modal-footer">
				<button type="submit" class="btn btn-primary" >Save</button>
				<button type="button" class="btn" data-dismiss="modal">Cancel</button>
			</div>		
	  </form>
	</div>
	
	<div id="treeModal" class="modal hide fade" data-backdrop="static" data-width="260" data-height="210" tabindex="-1" data-focus-on="button:first" >
		<div class="modal-header">
			<h3>Select map:</h3>
		</div>
		<div class="modal-body">
			<div class="tree-holder"></div>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-primary" data-action="select">OK</button>
			<button type="button" class="btn" data-dismiss="modal">Cancel</button>
		</div>
	</div>
	
	<div id="regionsModal" class="modal hide fade" data-backdrop="static" data-height="370" tabindex="-1" data-focus-on="button:first">
		<div class="modal-header">
			<h3></h3>
		</div>
		<div class="modal-body">
           <table  class="table table-condensed table-bordered"  >
                                
                <thead>
                  <tr>
                    <th class="id-column">Id:</th>
                    <th>Name:</th>
                  </tr>
                </thead>
                
                <tbody>  
                </tbody>
            </table>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-primary" data-action="select">OK</button>
			<button type="button" class="btn" data-dismiss="modal">Cancel</button>
		</div>
	</div>
	
	<div id="imageExplorerModal" class="modal hide" data-backdrop="static" data-width="980" data-height="370" tabindex="-1" data-focus-on="button:first" >
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" >&times;</button>
			<h3 data-state="icons">Icons</h3>
			<h3 data-state="images">Images</h3>
			<div class="btn-group">
				<a class="btn btn-small" href="#remove"><i class="icon-trash"></i> Delete</a>
			</div>
		</div>
		<div class="modal-body">
			<ul class="thumbnails">

			</ul>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-primary" data-action="select" data-dismiss="modal">OK</button>
			<button type="button" class="btn" data-dismiss="modal" data-action="cancel">Cancel</button>
		</div>
	</div>
	
	<div id="deleteModal" class="modal hide fade" data-backdrop="static" tabindex="-1" data-focus-on="button:first" >
		<div class="modal-header">
			<h3><img src="img/info.png" alt="info" /> Confirm Delete</h3>
		</div>
		<div class="modal-body">
			<p>Do you really wanna delete record?</p>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-danger" data-action="yes" data-dismiss="modal" >Yes</button>
			<button type="button" class="btn" data-dismiss="modal" >No</button>
		</div>
	</div>
	
	<div id="uploadModal" class="modal hide fade" data-backdrop="static" data-height="250" tabindex="-1" data-focus-on="button:first" >
        <div class="modal-header">
        	<button type="button" class="close" data-action="close" >&times;</button>
            <h3 data-state="image">Upload images</h3>
            <h3 data-state="icon">Upload icons</h3>
            <h3 data-state="map">Upload map image</h3>
        </div>
        <div class="modal-body">
            <a type="button" class="btn" data-action="browse">Select files to upload...</a>
            &nbsp;
            <div class="info-tip" data-state="icon"><i class="icon-info-sign" ></i> You can upload icons of max size 128x128, larger ones will be resized to fit the required size.</div>
            <div class="info-tip" data-state="image"><i class="icon-info-sign" ></i> You can upload images of max size 128x128, larger ones will be resized to fit the required size.</div>
            <div class="alert" data-state="map">
              <strong>Warning!</strong> Changing map image may cause that positions of map elements may not fit into new map image. Map image minimum size is 512x512px. See manual for details about uploading map image.
        </div>
            <div data-action="upload-container" ></div>
        </div>
        <div class="modal-footer" >
            <button type="button" class="btn btn-primary" data-action="close" >OK</button>
        </div>
    </div>
	
	<div id="unsavedModal" class="modal hide fade" data-backdrop="static" tabindex="-1" data-focus-on="button:first">
		<div class="modal-header">
			<h3>Unsaved changes</h3>
		</div>
		<div class="modal-body">
			<p>There are unsaved changes which will be lost if you continue.</p>
			<p>Do you want to continue?</p>
		</div>
		<div class="modal-footer">
			<button id="unsavedContinue" href="#" class="btn" data-dismiss="modal">Continue</button>
			<button href="#" class="btn btn-primary" data-dismiss="modal">Cancel</button>
		</div>
	</div>
	
	<div id="messageModal" class="modal hide fade" data-backdrop="static" tabindex="-1" data-focus-on="button:first">
		<div class="modal-header">
			<h3 data-state="oldBrowser">Outdated browser detected</h3>
			<h3 data-state="uploading map-noMap map-disabled select-map"><img src="img/info.png" alt="info" /> Info</h3>
		</div>
		<div class="modal-body" data-state="oldBrowser">
			<p>You are using an outdated browser.</p>
			<p>Using your current browser you may have limited access to all features of this application.</p>
		</div>
		<div class="modal-body" data-state="uploading">
			<p>You're uploading, please wait until upload is competed or cancel uploading.</p>
		</div>
		<div class="modal-body" data-state="map-noMap">
			<p>Selected map don't have the image uploaded.</p>
		</div>
		<div class="modal-body" data-state="map-disabled">
			<p>Selected map is disabled.</p>
		</div>
		<div class="modal-body" data-state="select-map">
			<p>Please select map first.</p>
		</div>
		<div class="modal-footer">
			<button href="#" class="btn btn-primary" data-dismiss="modal">OK</button>
		</div>
	</div>
	
    <div id="loadingModal" class="modal hide" data-backdrop="static" data-width="50" data-height="50" tabindex="-1">
        <img src="../common/img/progress_48.gif" alt="loading image" />
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
    	<img alt="logo" src="../common/img/logo.png" />
    	<div class="progress progress-striped active">
		  <div class="bar" style="width: 100%;"></div>
		</div>
    </div>
	
    <div class="no-cookies">
        This application requires cookies to be enabled.
    </div>
    
    
	
	<noscript>
		<div class="no-js">
			It appears that your browser has JavaScript disabled.<br />
			This Website requires your browser to be JavaScript enabled. 
		</div>
	</noscript>
	
	<script src="../common/js/jquery-1.11.3.min.js"></script>
	<script src="../common/js/jquery.jstree.js"></script>
	<script src="../common/js/bootstrap.js"></script>
	<script src="../common/js/bootstrap-adds.js"></script>
	<script src="../common/js/leaflet.js"></script>
	<script src="js/uploader.js"></script>
	<script src="js/jquery.validate.min.js"></script>
	<script src="js/jquery.simplecolorpicker.js"></script> 
	<script src="js/jquery.dataTables.min.js"></script>
	<script src="../common/js/common-plugins.js"></script>
	

	<?php if (getenv('DEVELOPMENT_ENV')): ?>
		<script src="../!dev/common/js/leaflet-conf.js"></script>
		<script src="../!dev/common/js/Templates.js"></script>
		<script src="../!dev/common/js/serviceCtrl.js"></script>
		<script src="../!dev/common/js/mapViewerCtrl.js"></script>
		<script src="../!dev/common/js/modalCtrl.js"></script>
		<script src="../!dev/common/js/viewCtrl.js"></script>
		
		<script src="../!dev/admin/js/admin.js/dataTableUtil.js"></script>
		<script src="../!dev/admin/js/admin.js/formValidator.js"></script>
		<script src="../!dev/admin/js/admin.js/serviceCtrl.js"></script>
		<script src="../!dev/admin/js/admin.js/Templates.js"></script>
		<script src="../!dev/admin/js/admin.js/treeCtrl.js"></script>
		<script src="../!dev/admin/js/admin.js/mapViewerCtrl.js"></script>
		<script src="../!dev/admin/js/admin.js/loginTabCtrl.js"></script>
		<script src="../!dev/admin/js/admin.js/mapsTabCtrl.js"></script>
		<script src="../!dev/admin/js/admin.js/mapEditTabCtrl.js"></script>
		<script src="../!dev/admin/js/admin.js/mapViewTabCtrl.js"></script>
		<script src="../!dev/admin/js/admin.js/markerTypesTabCtrl.js"></script>
		<script src="../!dev/admin/js/admin.js/markerTypeEditTabCtrl.js"></script>
		<script src="../!dev/admin/js/admin.js/dictionariesTabCtrl.js"></script>
		<script src="../!dev/admin/js/admin.js/dictionaryEditTabCtrl.js"></script>
		<script src="../!dev/admin/js/admin.js/markersTabCtrl.js"></script>
		<script src="../!dev/admin/js/admin.js/markerEditTabCtrl.js"></script>
		<script src="../!dev/admin/js/admin.js/labelsTabCtrl.js"></script>
		<script src="../!dev/admin/js/admin.js/labelEditTabCtrl.js"></script>
		<script src="../!dev/admin/js/admin.js/regionsTabCtrl.js"></script>
		<script src="../!dev/admin/js/admin.js/regionEditTabCtrl.js"></script>
		<script src="../!dev/admin/js/admin.js/settingsTabCtrl.js"></script>
		<script src="../!dev/admin/js/admin.js/passwordTabCtrl.js"></script>
		<script src="../!dev/admin/js/admin.js/logoutTabCtrl.js"></script>
		<script src="../!dev/admin/js/admin.js/utils.js"></script>
		<script src="../!dev/admin/js/admin.js/paramEditModalCtrl.js"></script>
		<script src="../!dev/admin/js/admin.js/uploadModalCtrl.js"></script>
		<script src="../!dev/admin/js/admin.js/treeModalCtrl.js"></script>
		<script src="../!dev/admin/js/admin.js/regionsModalCtrl.js"></script>
		<script src="../!dev/admin/js/admin.js/imageExplorerModalCtrl.js"></script>
		<script src="../!dev/admin/js/admin.js/modalCtrl.js"></script>
		<script src="../!dev/admin/js/admin.js/viewCtrl.js"></script>
		<script src="../!dev/admin/js/admin.js/start.js"></script>
		
		<script src="../!dev/admin/js/plugins.js/plugins.js"></script>
	<?php else : ?>
		<script src="../common/js/common.js"></script>
		<script src="js/plugins.js"></script>
		<script src="js/admin.js"></script>
	<?php endif; ?>


  </body>
</html>

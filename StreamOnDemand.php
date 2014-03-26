<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Strict//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>

<head>
	<!-- @AUTHOR: Christian Wallervand -->
	<link rel="stylesheet" type="text/css" href="style/streamer.css" />
	<!-- <link rel="stylesheet" type="text/css" href="style/jquery-ui-1.8.6.custom.css" /> -->
	<meta http-equiv="Content-Type" content="audio/mpeg; charset=UTF-8">
	<title>StreamOnDemand BETA</title>
	<!-- Include SM2 library -->
	<script type="text/javascript" src="script/soundmanager2.js"></script>
	<!-- Include custom javascript -->
	<script type="text/javascript" src="script/ondemand.js"></script>
	<!-- Include jQuery -->
	<script type="text/javascript" src="script/jquery-1.4.4.js"></script>
	<!-- Include jQuery Address -->
	<script type="text/javascript" src="script/jquery.address-1.3.js"></script>
	
	<script type="text/javascript" charset="UTF-8">
	
		initSM2();
		//parameter namses in url request
		var parNames;
		//number of parameter names in URL request
		var parLength;
		//Used to handle behavior when user goes back
		var internalSet;
		var PAUSE_IMG = 'img/pause.png';
		var PLAY_IMG = 'img/play.png';

		//NEW CODE
		//These variables are just to handle behaviour when user goes back in the browser
		//after playing different broadcast for a show
		var lastParameterLength = 0;
		var currentParameterLength = 0;
		//These variables are just to hande behaviour when user reloads after playing different
		//different broadcasts for a show, this is to prevent the application to 
		//show the main page and instead show the same show page and play the sound 
		var urlCurrParameters;
		var urlLastParameters;

		
		
		// Init and change handlers
		//Page first is entered
		$.address.init(function(e) {
			parNames = $.address.parameterNames();
			parLength = $.address.parameterNames().length;
			currentParameterLength = parLength;
			urlCurrParameters = $.address.value();
			
			//Main page is entered
			if (parLength == 0) {
				$.ajax({type: 'POST',
					url: '/ShowController.php',
					success: function(data) {
						$('#contentDiv').html(data);
						colourTable('showsTable');
						
					}
				});
			}
			//A show is chosen (via the URL)
			if (parLength > 0 && parNames[0] == 'showID') {
				var sID = $.address.parameter(parNames[0]);
				setSelectedShow(sID);
				$.ajax({
					  type: 'POST',
					  url: '/ShowController.php',
					  data: {showID: sID},
					  success: function(data) {
						  $('#contentDiv').html(data);
						  colourTable('broadcastsTable');
						  
					  }
				});
				//In addition to a show, a broadcast is chosen (via the URL)
				if ((parLength == 2 || parLength == 3) && parNames[1] == 'broadcastID') {
					$.ajax({
						type: 'POST',
						url: '/ShowController.php',
						data: {showID: sID},
						success: function(data) {
							$('#contentDiv').html(data);
							colourTable('broadcastsTable');
							var bID = $.address.parameter(parNames[1]);
							var file = $('#'+bID).attr('class');
							playSound(bID, file, $('#broadcastTitle'+bID).html());
						}
					});
				}
			}
		}).internalChange(function(e) {
			parLength = e.parameterNames.length;
			lastParameterLength = currentParameterLength;
			currentParameterLength = parLength;
			urlLastParameters = urlCurrParameters;
			urlCurrParameters = $.address.value();
			
			//Set internalSet when a sound is chosen internaly
			if (parLength == 3) {
				internalSet = true;
			}
		
		/**********
		 *EXTERNAL*
		 **********/
		//Handler for back and forward//
		}).externalChange(function(e) {
			parNames = e.parameterNames;
			parLength = parNames.length;
			lastParameterLength = currentParameterLength;
			currentParameterLength = parLength;
			urlLastParameters = urlCurrParameters;
			urlCurrParameters = $.address.value();
		
			
			//In case of reload when sound is playing, internalSet must be given a value
			if (parLength == 3 && parNames[2] == 'internalSet') {
				internalSet = true;
			}

			
			//User goes forward
			if (currentParameterLength > lastParameterLength) {
				sID = $.address.parameter(parNames[0]);
				$.ajax({
					type: 'POST',
					url: '/ShowController.php',
					data: {showID: sID},
					success: function(data) {
						$('#contentDiv').html(data);
						colourTable('broadcastsTable');	
					}
				});
			}
			
			//Comes from chosen show or chosen show and playing sound
			//Shows main page
			//Show table is shown
			//internalSet is a constraint because if one reloads broadcast page when internalSet is not set,
			//main page will be shown
			if (e.value == '/' || (parLength == 1 && parNames[0] == 'showID' && internalSet)) {
				$.ajax({
					type: 'POST',
					url: '/ShowController.php',
					success: function(data) {
						$('#contentDiv').html(data);
						colourTable('showsTable');
						
					  }
				});
				//User goes back
				if (lastParameterLength > currentParameterLength) {
					//Before going back, the last url must not be stored so that the user can go forward after going back
					$.address.history(false);
					$.address.value("/");
					//Re-enable history
					$.address.history(true);
					
				}
			}
		
			//When user goes forward from mainpage to the last selected broadcastpage
			//!internalSet is a constraint because if one goes back after chosing a sound,
			//the broadcast page would be show instead og the show page 
			if (parLength == 1 && parNames[0] == 'showID' && !internalSet) {
				sID = $.address.parameter(parNames[0]);
				//window.alert("if-test");
				//NEW CODE
				//lastParameterLength = currentParameterLength;
				//currentParameterLength = parLength;
				$.ajax({
					type: 'POST',
					url: '/ShowController.php',
					data: {showID: sID},
					success: function(data) {
						$('#contentDiv').html(data);
						colourTable('broadcastsTable');
						
					}
				});
			}
			//When a user goes (from main page) forward to the broadcast page he last visited,
			//or user reloads page when playing a sound
			if (parLength == 3 && parNames[0] == 'showID' && parNames[1] == 'broadcastID' && internalSet == true) {
				bID = $.address.parameter(parNames[1]);
				sID = $.address.parameter(parNames[0]);
		
				//This if block handles behaviour when a user goes back(lastParameterLength >= lastParameterLength && lastParameterLength == 3)
				//or reloads page (urlCurrParameters != urlLastParameters)
				//after playing several broadcasts for a show
				//
				if (lastParameterLength >= lastParameterLength && lastParameterLength == 3 && urlCurrParameters != urlLastParameters) {
					//Enables the user to go forward to the last selected show
					//Change the url when user goes back
					//$.address.value("?showID="+$.address.parameter(parNames[0]));
					//Before going back, the last url must not be stored so that the user can go forward after going back
					$.address.history(false); 
					$.address.value("/");
					//Re-enable history 
					$.address.history(true); 
					
					$.ajax({
						type: 'POST',
						url: '/ShowController.php',
						success: function(data) {
							$('#contentDiv').html(data);
							colourTable('showsTable');
							
						}
					});
				}
				//NEW 
				else {
					$.ajax({
						type: 'POST',
						url: '/ShowController.php',
						data: {showID: sID},
						success: function(data) {
							$('#contentDiv').html(data);
							colourTable('broadcastsTable');
							highlightTr(bID);
							//To show the pausebutton in a row when sound is playing
							updateTrPlayPause(bID, PAUSE_IMG);
							//Show the Play button in the row if the sound is paused
							if (getMySound().paused) {
								updateTrPlayPause(bID, PLAY_IMG);
							}
						
						}
					});
				}
			}
		});
</script>

<script type="text/javascript">
   /*******************
	* Google Analytics*
	*******************/
	var _gaq = _gaq || [];
  	_gaq.push(['_setAccount', 'GOOGLE_ANALYTICS_ACCOUNT_NUMBER']);
  	_gaq.push(['_trackPageview']);

  	(function() {
    	var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    	ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    	var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  	})();
</script>
</head>
<body>
<div id="main">
	<div id="headerDiv" class="header">
		<div id="playerDiv" class="header">
			<img id="playerPlayPause" src="img/playGray.png" onclick="playPause()"/>
			<p id="playedTime" class="time">0:00:00</p>
			<div id="progressBarContainer" class="progressBar">
				<div id="progressBar" class="progressBar"></div>
				<div id="loadBar" class="progressBar"></div>
			</div>
			<p id="totalTime" class="time">0:00:00</p>
			<img id="volumeDown" class="volumeButton" src="img/volumeDown.png" />
			<img id="volumeUp" class="volumeButton" src="img/volumeUp.png" />
			<a href="http://www.radiorevolt.no"><img id="rrLogo" src="img/rrLogo.png" /></a>
		</div>
	
	</div>
	<div id="filterDiv" class="header">
		<p id="mainLinkP" onclick="getMainPage()">Til programoversikten</p>
	</div>
	<div id="contentDiv" class="content"></div>
</div>
<script type="text/javascript">
	/**********************************************************************************
	 * This is an eventhandler for the progressbar									  *	
	 * Used when the progressbar is clicked to change the position of the played sound*
	 **********************************************************************************/
	//The X position of the progressbar where the mouseclick is done
	//Given as a double where 0 < clickedX < 1
	 var clickedX;
	 //The soundmanager sound object
	 var sound;
	 //The estimated time of a sound in ms
	 var estimate;
	 //The new playhead of the sound in ms
	 var newSoundPos;
	 /***********************************************************************
	  * A jQuery click event												*
	  * Handles the behaviour of the progressbar when it is clicked			*
	  * Moves the playhead of the sound									    *
	  * Alters the progressbar length according to the position of the sound*
	  ***********************************************************************/

	$('#progressBarContainer').click(function(e) {
		clickedX = ((e.pageX - this.offsetLeft)/$(this).width());
		sound = getMySound();
	    estimate = sound.durationEstimate;
	   	newSoundPos = estimate * clickedX;
	  	sound.setPosition(newSoundPos);
	    $('#progressBar').css('width', ''+clickedX+'%');
	});	
	
	 var volume;
	/**************************************
	 * Handler for volume buttons         *
	 * Used when volume buttons are pushed*
	 **************************************/
	$('#volumeUp').mousedown(function(e) {
		sound = getMySound();
		sound.setVolume(sound.volume + 10);
		//window.alert("Volume up:" + sound.volume);
	});
	$('#volumeDown').mousedown(function(e) {
		//sound.volume = volume--;
		sound = getMySound();
		sound.setVolume(sound.volume - 10);
		//window.alert("mouse down");
		//window.alert("Volume down: " + sound.volume);
	});

</script>
</body>
</html>

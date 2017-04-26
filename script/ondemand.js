/************************************************************************************
 * @AUTHOR Christian Wallervand														*
 * This script contains custom JavaScript methods for the StreamOnDemand application*
 ************************************************************************************/
var SWF_PATH            = 'ondemand.radiorevolt.no/swf'; // Example: ondemand.radiorevolt.no/swf/
var SOUND_PATH          = 'filer.radiorevolt.no/ondemand/'; // Example: pappagorg.radiorevolt.no/ondemand/
var PLAY_IMG			= 'img/play.png';
var PAUSE_IMG			= 'img/pause.png';
var TR_COLOUR_1 		= '#FFFFFF';
var TR_COLOUR_2 		= '#F0F0F0';
var TR_COLOUR_HIGHLIGHT = '#C0C0C0';
//The program selected
var selectedShow = 0;
//Is used to keep track of which sound is playing
var soundPlayingID = 0;
//The Sound Manager object
var mySound;
//Boolean variable used to play and pause a sound 
var playing = false;
var appendToURL = '';
//The total time of a sound in minutes and seconds
//var totMinutes = 0;
//var totSeconds = 0;
///var totHours = 0;
//The position of the sound in ms
var milliSecs;
//Played seconds for a sound
var numSecs;
//Played minuts
var numMins;
//Played hours
var numHours;
//Time played (string)
var resultTime;
var MS_SECS = (1000);
var MS_MINS = (MS_SECS * 60);
var MS_HOURS = (MS_MINS * 60);

/****************************
 * Returns the created sound*
 ****************************/
function getMySound() {
	return mySound;
}

/*****************************
 * Initialize Sound Manager 2*
 *****************************/
function initSM2() {
	soundManager.debugMode = false; 							//disable or enable debug output
	soundManager.url = SWF_PATH; 								//path to directory containing SM2 SWF
	soundManager.flashVersion = 9; 								//optional: shiny features (default = 8)
	soundManager.useFlashBlock = false; 						//optionally, enable when you're ready to dive in
	soundManager.useHTML5Audio = true;
	soundManager.html5Test=/(probably|maybe)/i;
}
/*************************
 * Sets the selected show*
 *************************/
function setSelectedShow(show) {
	selectedShow = show;
}

/**********************************************
 * Pause and resume a sound				      *
 * Changes img when sound is paused/resumed   *
 * To be used when a sound is allready playing*
 **********************************************/
function playPause(id) {
	//A sound is chosen
	if (soundPlayingID != 0) {
		if (playing) {
			updateTrPlayPause(soundPlayingID, PLAY_IMG);
			//Change the player playbutton
			$('#playerPlayPause').attr('src', PLAY_IMG);
			mySound.pause();
			playing = false;
		}
		else {
			updateTrPlayPause(soundPlayingID, PAUSE_IMG);
			$('#playerPlayPause').attr('src', PAUSE_IMG);
			mySound.play();
			playing = true;
		}
	}
}

function playNextSound(id) {
	var rows = document.getElementsByTagName("tr");
	var nextrow_id;
	var currow_index;

	for (var j = 0; j < rows.length; j++) {
		if (String(rows[j].id) == String(id)) {
			currow_index = j;
			break;
		}
	}

	if (currow_index == rows.length-1) {
		nextrow_id = id;
	} else {
		nextrow_id = rows[currow_index+1].id;
	}

	window.location.assign('/#/?showID='+selectedShow+'&broadcastID='+nextrow_id);
}

/**************************
 * Play the selected sound*
 **************************/
function playSound(id, file, broadcastTitle) {
	//Sound Manager 2 is supported
	if (soundManager.supported()) {
		var path = SOUND_PATH + file;
		var loadedPercent;
		var playedPercent;
		//Append parameters for show and broadcast to the URL
		appendToURL = '?showID='+selectedShow+'&broadcastID='+id+'&internalSet=1';
		$.address.value(appendToURL);
		//Every time a sound is chosen the table must be re-coloured
		colourTable('broadcastsTable');
		//Highlight the selected broadcast
		highlightTr(id);
		//Change the player playbutton
		$('#playerPlayPause').attr('src', PAUSE_IMG);
		//Change the playbutton to pause in the selected row
		updateTrPlayPause(id, PAUSE_IMG);
		
		nowPlaying(broadcastTitle);
		
		//First time a sound is chosen
		if (soundPlayingID == 0) {
			createSound(id, path);
			mySound.play();
			soundPlayingID = id;
			//Change cursor to pointer for clickable elements when a sound is played
			$('#progressBarContainer').css('cursor', 'pointer');
			$('#playerPlayPause').css('cursor', 'pointer');
			$('.volumeButton').css('cursor', 'pointer');
			playing = true;	
			updateTrPlayPause(id, PAUSE_IMG);
		}
		//Some sound is allreday playing
		else {
			//A new sound is chosen
			if (!soundPlaying(id)) {
				//If another sound was played before a new wa s chosen, the play/pause button for that sound should be set to play
				if (soundPlayingID != 0) {
					updateTrPlayPause(soundPlayingID, PLAY_IMG);
				}
				//Update the sound id
				soundPlayingID = id;
				//Destroy the sound allready playng
				mySound.destruct();
				//Create the new sound
				createSound(id, path);
				mySound.play();
				playing = true;
				
			}
			//Same sound is chosen
			else {
				playPause(id);
				//playing = true;
			}
		}
	}
}
/*******************************************************
 * Creates and returns a sound based on id and filepath*
 *******************************************************/
function createSound(id, path) {

	function playNextSound() {
        var rows = document.getElementsByTagName("tr");
        var nextrow_id;
        var currow_index;

        for (var j = 0; j < rows.length; j++) {
                if (String(rows[j].id) == String(id)) {
                        currow_index = j;
                        break;
                }
        }

        if (currow_index == rows.length-1) {
                nextrow_id = id;
        } else {
                nextrow_id = rows[currow_index+1].id;
        }

        window.location.assign('/#/?showID='+selectedShow+'&broadcastID='+nextrow_id);
	window.location.reload();
	}

	mySound = soundManager.createSound({
		//SM2 has loaded - now you can create and play sounds
		id: id,
		url: path,
		volume: 100,
//		multiShotEvents: true,
		onfinish: playNextSound,
		whileloading: function() {
			//loadedPercent = parseFloat((this.bytesLoaded/this.bytesTotal)*100);
			//window.alert(loadedPercent);
			//$('#loadBar').css('width', ''+loadedPercent+'%');
			//The total time of the sound beeing played
			//$('#totalTime').html(test);
		},
		whileplaying: function() {
			//Should be in whileloading, but there seems to be problems in every browser except FF
			loadedPercent = parseFloat((this.bytesLoaded/this.bytesTotal)*100);
			$('#loadBar').css('width', ''+loadedPercent+'%');
			
			//window.alert("bytesLoaded: " + this.bytesLoaded + " bytesTotal: " + this.bytesTotal);
			$('#totalTime').html(soundTime(this.durationEstimate));
			//Played time
			$('#playedTime').html(soundTime(this.position));
			playedPercent = parseFloat((this.position/this.durationEstimate)*100);
			$('#progressBar').css('width', ''+playedPercent+'%');
		},
	});
	return mySound;
}

/*********************************************************************
 * Makes an AJAX call that show the main page of the SoD application *
 *********************************************************************/
function getMainPage() {
	//Before going back, the last url must not be stored so that the user can go forward after going back
	//$.address.history(true);
	$.address.value("/");
	//Re-enable history
	//$.address.history(true);
	$.ajax({type: 'POST',
		url: '/ShowController.php',
		success: function(data) {
			$('#contentDiv').html(data);
			colourTable('showsTable');
		}
	});
	
}

/*************************************
 * Returns true if the selected sound*
 * is allready playing               *
 *************************************/
function soundPlaying(sID) {
	return sID == soundPlayingID;
}

/***********************************************************
 * Is called when a show is chosen				   		   *
 * 														   *
 ***********************************************************/
function getShowInfo(id) {
	appendToURL = '?showID='+id;
	$.address.value(appendToURL);
	$.ajax({
		  type: 'POST',
		  url: '/ShowController.php',
		  data: {showID: id},
		  success: function(data) {
			  $('#contentDiv').html(data);
			  colourTable('broadcastsTable');
			  window.scrollTo(0, 0);
			  //When a sound is allready playing
			  if (soundPlayingID != 0) {
				  //the pause button for the row must be shown
				  //and the row must be highlighted
				  updateTrPlayPause(soundPlayingID, PAUSE_IMG);
				  highlightTr(soundPlayingID);
				  //Append parameters for show and broadcast to the URL
				  //So that when comming back to the sound listened to, it it possible to reload the sound
				  appendToURL = '?showID='+id+'&broadcastID='+soundPlayingID+'&internalSet=1';
				  $.address.value(appendToURL);
				  if (!playing) {
					  updateTrPlayPause(bID, PLAY_IMG);
				  }
			  }
			  
		  }
	});
	cssShowInfoDiv();
	selectedShow = id;
}

function nowPlaying(broadcastTitle) {
	if (soundPlayingID == 0) {
		$('#playerDiv').append('<p id=\'nowPlayingP\'>Spilles: ' + broadcastTitle +'</p>');
	}
	else {
		$('#nowPlayingP').html('Spilles: ' + broadcastTitle);
	}
}


/***********************************************************
 * Given a input in milliseconds, this function			   *
 * returns a string holding the time in the format H:MM:SS *
 * Used to show played time and total time				   *	
 ***********************************************************/
function soundTime(ms) {
	milliSecs = ms;
	numHours = Math.floor(milliSecs/MS_HOURS);
	numMins = Math.floor((milliSecs - (numHours * MS_HOURS)) / MS_MINS);
	numSecs = Math.floor((milliSecs - (numHours * MS_HOURS) - (numMins * MS_MINS))/ MS_SECS);
	
	if (numSecs < 10) {
	  numSecs = "0" + numSecs;
	}
	if (numMins < 10) {
	  numMins = "0" + numMins;
	}
	resultTime = numHours.toString() + ":" + numMins.toString() + ":" + numSecs.toString();
	return resultTime;
}

/********************************
 * Imports CSS from streamer.css*
 * Used when a show is chosen   *
 ********************************/
function cssShowInfoDiv() {
	//CSS for the gray boxes when a show is chosen
	/*$('.grayBox').css('background-color');
	$('.grayBox').css('height');
	$('.grayBox').css('top');
	$('.grayBox').css('padding');
	$('.grayBox').css('position');
	$('.grayBox').css('z-index');
	
	//CSS for grayBoxContainer
	$('#grayBoxContainer').css('position');
	$('#grayBoxContainer').css('top');
	$('#grayBoxContainer').css('height');
	$('#grayBoxContainer').css('background-color');
	$('#grayBoxContainer').css('width');
	
	//CSS for the p tag in the player showin what i beeing played
	$('#nowPlayingP').css('color');
	$('#nowPlayingP').css('left');
	$('#nowPlayingP').css('font-family');
	$('#nowPlayingP').css('font-size');
	$('#nowPlayingP').css('position');
	$('#nowPlayingP').css('top');
	
	//CSS for the show name in the gray-box
	$('.showHeadline').css('font-size');
	$('.showHeadline').css('font');
	$('.showHeadline').css('width');
	
	//CSS for the show info in the gray-box
	$('.showInfoLong').css('font-family');
	$('.showInfoLong').css('font-size');
	$('.showInfoLong').css('width');
	
	//CSS for the td's in the broadcasts table
	$('.broadcastsTableTd1').css('width');
	$('.broadcastsTableTd2').css('width');
	$('.broadcastsTableTd2').css('padding-left');
	
	//CSS for the broadcast title in the broadcaststable td
	$('.broadcastNameP').css('font-family');
	$('.broadcastNameP').css('font-size');
	
	//CSS for the p element that contains the broadcast info
	$('.broadcastInfoP').css('font-family');
	$('.broadcastInfoP').css('font-size');
	
	//CSS for the div containing the gray boxes 
	$('#showInfoDiv').css('width');
	$('#showInfoDiv').css('left');
	
	//CSS for the show img in the gray-box
	$('#showImgLarge').css('width');
	$('#showImgLarge').css('height');
	
	//CSS for the gray box containing show info elements
	$('#showInfoContentDiv').css('width');
	$('#showInfoContentDiv').css('top');
	$('#showInfoContentDiv').css('left');
	
	//CSS for the gray-box that contains podcast info
	$('#podcastDiv').css('width');
	$('#podcastDiv').css('left');
	
	//CSS for the table containing broadcasts
	$('#broadcastsTable').css('position');
	$('#broadcastsTable').css('top');
	$('#broadcastsTable').css('width');
	$('#broadcastsTable').css('background-color');*/
}

/*********************************************
 * Updates the playbutton in a broadcast row *
 *********************************************/
function updateTrPlayPause(id, src) {
	$('#'+id+' img').attr('src', src);	
}

/*********************************************
 * Colours a selected tr					 *
 * Used when a sound is chosen				 *
 * Highlights the sound tr and the program tr*
 *********************************************/
function highlightTr(id) {
	document.getElementById(id).style.backgroundColor = TR_COLOUR_HIGHLIGHT;
}

/********************************************
 * Colours the rows in <table> with given id*
 ********************************************/
function colourTable(id) {
	var table = document.getElementById(id);  
	var rows = table.getElementsByTagName("tr"); 
	var length = rows.length;
	for(var i = 0; i < length; i++) {          
	    if(i % 2 == 0){
	    	rows[i].style.backgroundColor = TR_COLOUR_1;
	    }
	    else{
	    	rows[i].style.backgroundColor = TR_COLOUR_2;
	    }      
	} 
}




$(document).ready(function(e)
{
	$.swRefreshStatus();		// refresh the commit button status
	
	// init tabs
	$('#tabs, .tabs').tabs();
	
	// init menu's
	common_initializeMenus();
	common_configureDropDownButtons();
	
	// Generic buttons
	common_initButtons();
	
	$(document).live('click',function(e){	// This function closes the popup menu when you click anywhere else on the document
		$('.popupMenu').slideUp();
	});
	
	// generic accordion setup
	$(".accordion").accordion({ header: "h3", autoHeight: false, collapsible:true, active:false })
	
	// Selectable history items
	$( "#selectable" ).selectable();

	//hover states on the static widgets
	$('#dialog_link, ul#icons li, #sortable li, #selectable li').hover(
		function() { $(this).addClass('ui-state-hover'); }, 
		function() { $(this).removeClass('ui-state-hover'); }
	);
	
	
// Status bar commit button ///////////////
	$( "#noChangeDialog, #passwordDialog" ).dialog({autoOpen: false,modal: true,draggable: false,resizable: false });				// init
	$( "#changeDialog" ).dialog({autoOpen: false,modal: true,draggable: false,resizable: false, width:700 });						// init
	$(".selectAll, .deselectAll, .previewChanges, .commitChanges, .passwordCancel").button();										// init
	$("#status_bar div").css("opacity",0.7);																						// set bar opacity
	$("#status_commit").hover(function (){$(this).css("opacity",1);},function (){$(this).css("opacity",0.7);})						// commit button rollover
	$('#commitDeselectAll').click(function(){$('#sessionChanges input[type=checkbox]').removeAttr('checked')});						// select/deslect all
	$('#commitSelectAll').click(function(){$('#sessionChanges input[type=checkbox]').attr('checked','checked')});						// select/deslect all
	
	
// Status bar account button //////////////
	$("#status_logout").hover(function (){$(this).css("opacity",1);},function (){$(this).css("opacity",0.7);})				// acocunt button rollover
		.click(function(){
			$('#userOptions').slideToggle();return false;
		}).css("cursor","pointer");							// open menu



	
// editable text areas ////////////////////
	$(".editSection, .editable").addClass("ui-corner-all");																	// init
	$(".editable").hover(function(){$(this).css("border-color","#FAA");},function(){$(this).css("border-color","#CCC");})	// init
		.focusin(function() {$(this).css("border-color","#FAA").css("border-style","solid")})								// init
		.focusout(function() {$(this).css("border-color","#CCC").css("border-style","dashed")})								// init
	$(".undoChange:not(.ui-state-disabled)").button({text:false,icons:{primary:'ui-icon-arrowreturnthick-1-w'}}).attr("title","Undo")	// init
	$(".undoChange.ui-state-disabled").button({text:false,disabled:true,icons:{primary:'ui-icon-arrowreturnthick-1-w'}}).attr("title","Undo")	// init
	
	
	
	
	$("#swStatus").css("opacity",0);
	var offsetTitle = $("#swTitle").offset();
	var offsetStatus = $("#swStatus").offset();
	var offset =  offsetStatus.top - offsetTitle.top;
	function updateStatusReturn() {
		$("#swStatus").fadeOut("slow").slideDown("slow");
		$("#swTitle").fadeIn("slow").animate({top:offset},"slow");
	}
	function updateStatus(m) { //////////////////// NOT WORKING CORRECTLY ///////////////////////////////////////////
		$("#swTitle").fadeOut("slow").slideUp("slow");
		$("#swStatus").html(m).fadeIn("slow").animate({top:-offset},"slow",function(){
			setTimeout(function(){updateStatusReturn();},5000);});
	}////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	
	// Password strength checker //////////////
	$(".passwordChange").button({disabled:true}).css("height", 24);
	jQuery("#newPW").passwordValidate();
	jQuery("#newPW2").keyup(function(e) {
		if ($(this).val() == $("#newPW").val()) {
			$(this).css("color","#009900");
			$(".passwordChange").button({disabled:false});
		}
		else {
			$(this).css("color","#000000");
			$(".passwordChange").button({disabled:true});
		}
	});
	
	$('#commitChanges').click($.swCommit);		//  bind the commit button click event
	
	$('#div_main_loading').fadeOut(function(){
		$('#div_main_content').fadeIn();
	});
	
});

// Ajax change password //////////////////////
function changePasswordAjax () {
	if ($("#newPW").val() != $("#newPW2").val()) alert("ajax required to update password");
}

function pwdFn() {
	$( "#passwordDialog" ).dialog("open");
	$(".ui-dialog").position({ my: 'center top', at: 'center top', of: '#main', offset: "0, 10" });
	return false;
}

 
 
// Logging out message ///////////////////////
var logoutHTML = '<h2>Thank you</h2><img src="images/ajax-loader2.gif" alt="Logging out" style="margin-right:15px" />Logging out...';
function logoutFn() {
	$('#div_main_content').fadeOut(function(){
		$('#div_main_loading').html(logoutHTML).fadeIn();
	});
	window.location.href = 'logout.php';
}








// ScratchWebs jQuery Plugin (Core Functions)
(function($){
	
	var sw = {
		isUpdating:false,
		isRefreshing:false
	}
	
	// ajax manager to queue ajax calls to prevent the web browser from crashing
	var ajaxManager = $.manageAjax.create('cacheQueue', {
		queue: true, 
		cacheResponse: false,	// don't ever cache the response as it will always be different
		preventDoubleRequests: false,
		maxRequests:2
	})
	
	// Loading dropdown message
	$(document).ready(function(e) {
		$("#div_update_loading").ajaxStart(function(){
			if (sw.isUpdating) {
				$(this).slideDown('fast');
			}
		});
		$("#div_update_loading").ajaxStop(function(){
			if (sw.isUpdating){
				$.swRefreshStatus.call(this);
				sw.isUpdating=false;
			} 
			else if (sw.isRefreshing){
				sw.isRefreshing=false;
				$(this).slideUp('fast');
			}
		});
    });
	
	// All ajax calls will use this function
	// EXAMPLE...
	// $.swAjax('serverScript.php',{param1:1,param2:'two'},function(){
	//		Do something when the ajax call has finished
	//	})
	$.swAjax = function( url, data, callback, dataType ){
		dataType = (typeof dataType == 'undefined') ? 'html' : dataType;
		$.manageAjax.add('cacheQueue',{
			  url: url,
			  data: data,
			  type:'GET',
			  dataType:dataType,
			  success: function(response) {
				  if (typeof(callback) == 'function') callback.call(this,response);
			  },
			  error: function(data) {
				  alert(data.statusText + ': ' + data.responseText);
			  }
		});
	}
	
	// To refresh a control
	// EXAMPLE...
	//function swImage_refreshById(id) {
	//    $('#img_' + id).html('<img src="loading.gif" />');      // set loading message
	//    
	//    $.swRefreshControl({type:'swImage',id:id}, function(response){
	//        $('#img_' + imgID).replaceWith(response);
	//    });
	//}
	$.swRefreshControl = function( data, callback ){
		$.swAjax('refreshControl.php',data,function(response){
			if (typeof callback == 'function') callback.call(this,response);
		});
	}
	
	// To Update the session
	// EXAMPLE 1 (Basic usage)...
	//  function swSection_updateHtml(id,html)
	//  {
	//      var data = {
	//          update_object:'swSection',
	//          update_type:'replace_text',
	//          update_id:id
	//          update_value:html
	//      };
	//      
	//      $.swUpdateSession(data);	// no call back required as the text would have been changed on the page already
	//  }
	//
	//	EMAPLE 2 (More advanced usage)...
	//  function swImage_cropImage(id,theForm)
	//  {
	//      var formData = theForm.swSerialize();	// theForm needs to be a jquery object,
	//      										// serialize form input to a javascript object.
	//      var formData = $.extend({				// add some more data to the javascript object.
	//          update_object:'swImage',			// this step could be missed out if this data is
	//          update_object_id:id,				// hidden in the form
	//          update_type:'crop'
	//      },formData);
	//		
	//      $.swUpdateSession(formData,function (response){			// (optional) callback function
	//          $('#img_' + id).replaceWith(response.responseHTML);
	//      });
	//  }
	//	
	//	updateKey is the reference to the sessionUpdate (so we can undo it later)
	$.swUpdateSession = function( data, callback ){
		sw.isUpdating=true;
		$.swAjax('updateSession.php',data,function(response){
			var response = {
				updateKey:$('updateKey',response).text(),
				responseHTML:$('responseHTML',response).text()
			}
			
			if (typeof(callback) == 'function') callback.call(this,response);
		},'xml');
	}
	
	$.swUndo = function( data, callback ){
		sw.isUpdating=true;
		$.swAjax('undo.php',data,callback);
	}
	
	// Refresh commit status
	// Function get's called automatically after $.swUpdateSession has finished
	$.swRefreshStatus = function(){
		sw.isRefreshing=true;
		
		$.swAjax('refreshStatus.php',{},function(response){			// ajax call to refresh the status
			$("#status_commit").unbind('click')						// remove previous click event
			
			var noOfChanges = $('noOfChanges',response).text();		// get the number of changes from the response
			
			if (noOfChanges == 0) {
				$("#status_commit")
					.removeClass('ui-state-error')									// set the button style
					.addClass('.ui-state-disabled')									// set the button style
					.html('<span class="ui-icon ui-icon-disk"></span>' +			// set the button text
						  '<span class="commit_text">No Changes to Commit</span>')
					.click(function(){												// add new click event
						$("#noChangeDialog").dialog('open');return false;			// open no change dialog
					});
			} else {
				$("#status_commit")
					.removeClass('.ui-state-disabled')							// set the button style
					.addClass('ui-state-error')									// set the button style
					.html('<span class="ui-icon ui-icon-disk"></span>' +		// set the button text
						  '<span class="commit_text" style="#CC0000">Commit '+noOfChanges+' Changes')
					.click(function(){											// add new click event
						$("#changeDialog").dialog('open');
						$(".ui-dialog").position({ my: 'center top', at: 'center top', of: '#main', offset: "0, 10" });
						return false;			// open change dialog
					});
				
				$('#sessionChanges').html($('sessionChanges',response).text());
			}
			
		},'xml');
	}
	
	$.swCommit = function(){
		$('#sessionChanges form')[0].submit();
		
		// ajax commit
		/*var formData = $('#sessionChanges form').swSerialize();
		$.swAjax('commit.php',formData,function(response){
			alert(response);
		});*/
	}
	
	// function to serialize form data to a javascript object
	$.fn.swSerialize = function(object,querystring){
		var serializedData = {}
		
		this.each(function(){
			if (this.tagName == "FORM") {
				var formData = $(this).serialize()
				var regex_key_value = /([^&=]+)=?([^&]*)/g;
				
				var decode = function (string) {
					return decodeURIComponent(string.replace(/\+/g, " "));
				}
				
				while (keyVal = regex_key_value.exec(formData))
					serializedData[decode(keyVal[1])] = decode(keyVal[2]);
			}
		});
		
		return serializedData;
	}
	
})(jQuery)

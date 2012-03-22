$(document).ready(function(e){
	
	// RTE init and functions /////////////////
		$(".RTEbold, .RTEitalic, .RTEunderline").button({disabled:true});
		$(".RTEsave").button({text:false,icons:{primary:'ui-icon-disk'}}).attr("title","Save");
		$(".RTEundo.ui-state-disabled").button( "option", "disabled", true );
	
		window.RTEditors = $(".editRTE").rte({
			width: 605,
			height: 200,
			controls_rte: rte_toolbar
		});
		
		/* EXAMPLE GET HTML FROM SECTIONS
		for (var rte in RTEditors)
			if (typeof RTEditors[rte].get_content == 'function')
				alert(rte + ' = ' + RTEditors[rte].get_content());*/
				
		$(".editRTE").hover(function(){$(this).css("border-color","#FAA");},function(){$(this).css("border-color","#CCC");});	// init	
});

// ajax update
function swSection_html_update(id,undoButton)
{
	var rte = RTEditors['divSectionHTML' + id];		// get the RTE object
	
	var data = {
		update_object:'swSection',
		update_object_id:id,
		update_type:'section_update_html',
		value:rte.get_content()			// get the html from the RTE object
	};
	$.swUpdateSession(data,function(response){
	
		if (response.updateKey == ''){																											// if item was set back to original value
			undoButton.button("option","disabled",true ).data('updateKey','')																			// disable undo button
			.parent().parent().removeClass('ui-state-error').prev().removeClass('ui-state-error');														// remove highlighting
			if(response.noUpdates == 0) {																												// if there are no updates left on page
				$("#pgID_" + undoButton.parent().data('pageid') + " .changeIndicator").remove();																			// remove the changeIndicator
			}
			else {																																		// if there are updates left on the page
				$("#pgID_" + undoButton.parent().data('pageid') + " .changeIndicator").remove();																			// update changeIndicator
				$("#pgID_" + undoButton.parent().data('pageid')).append('<span class="changeIndicator ui-state-error">' + response.noUpdates + '</span>');
			}
		} else {																																// if item was changed
			undoButton.button("option","disabled",false ).data('updateKey',response.updateKey)															// enable undo button
			.parent().parent().addClass('ui-state-error').prev().addClass('ui-state-error');															// add highlighting	
			if($("#pgID_" + undoButton.parent().data('pageid') + " .changeIndicator").size()) {																	// update changeIndicator
				$("#pgID_" + undoButton.parent().data('pageid') + " .changeIndicator").remove();
				$("#pgID_" + undoButton.parent().data('pageid')).append('<span class="changeIndicator ui-state-error">' + response.noUpdates + '</span>');
			}
			else {
				$("#pgID_" + undoButton.parent().data('pageid')).append('<span class="changeIndicator ui-state-error">' + response.noUpdates + '</span>');
			}
		}
		
	});
}
function swSection_html_undo(id,undoButton)
{
	if (undoButton.hasClass('ui-state-disabled')) return;
	
	var data = {
			update_key:undoButton.data('updateKey'),
			update_object_id:id
	};
	
	$.swUndo(data,function(response){
		var rte = RTEditors['divSectionHTML' + id];																						// get the RTE object
		rte.set_content(response.undoResponse);																							// set the html
		undoButton.button("option","disabled",true ).data('updateKey','')																// disable undo button
		.parent().parent().removeClass('ui-state-error').prev().removeClass('ui-state-error');											// remove highlighting
		if($("#pgID_" + undoButton.parent().data('pageid') + " .changeIndicator").size()) {																// update changeIndicator
			if(response.noUpdates == 0) {																												// if there are no updates left on page
				$("#pgID_" + undoButton.parent().data('pageid') + " .changeIndicator").remove();																			// remove the changeIndicator
			}
			else {																																		// if there are updates left on the page
				$("#pgID_" + undoButton.parent().data('pageid') + " .changeIndicator").remove();																			// update changeIndicator
				$("#pgID_" + undoButton.parent().data('pageid')).append('<span class="changeIndicator ui-state-error">' + response.noUpdates + '</span>');
			}
		}
		else {
			$("#pgID_" + undoButton.parent().data('pageid')).append('<span class="changeIndicator ui-state-error">' + response.noUpdates + '</span>');
		}		
	});
}
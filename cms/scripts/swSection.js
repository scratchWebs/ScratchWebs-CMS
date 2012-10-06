function swSection_getById(id)
{
	return $('#sect' + id);
}
function swSection_setNoUpdates(id,noUpdates)
{
	var section = swSection_getById(id);
	
	var noUpdatesBefore = section.data('noUpdates');
	var noUpdatesNow = noUpdates;
	var noUpdatesDifference = common_diff(noUpdatesBefore,noUpdatesNow);

	if (noUpdatesBefore > noUpdatesNow)
		noUpdatesDifference = parseInt("-" + noUpdatesDifference);
	
	// set the new number of updates
	section.data('noUpdates',noUpdates);

	// adjust the relevent page noUpdates
	swPage_adjustNoUpdates(section.data('pageid'),noUpdatesDifference);
}

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
		swSection_setNoUpdates(id,response.noUpdates);		// update changeIndicator
		
		if (response.updateKey == ''){																	// if item was set back to original value
			undoButton.button("option","disabled",true ).data('updateKey','')							// disable undo button
			.parent().parent().removeClass('ui-state-error').prev().removeClass('ui-state-error');		// remove highlighting
		} else {																						// if item was changed
			undoButton.button("option","disabled",false ).data('updateKey',response.updateKey)			// enable undo button
			.parent().parent().addClass('ui-state-error').prev().addClass('ui-state-error');			// add highlighting
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
		var rte = RTEditors['divSectionHTML' + id];														// get the RTE object
		rte.set_content(response.undoResponse);															// set the html
		undoButton.button("option","disabled",true ).data('updateKey','')								// disable undo button
		.parent().parent().removeClass('ui-state-error').prev().removeClass('ui-state-error');			// remove highlighting

		swSection_setNoUpdates(id,response.noUpdates);
	});
}
$(document).ready(function(e) {
    
	// Sortable pages
	$("#sortable")
		.sortable({placeholder:"ui-state-highlight", update:function(event,ui) { 
			swPage_updatePageOrder($(this));
		}})
		.selectable()									// make li's selectable
		.children('li')									// now change jquery to look at the li's
		.click(function(){
			$( "#sortable li" ).removeClass('ui-state-highlight');		// set the style
			$(this).addClass('ui-state-highlight');						// set the style
			
			var pageId = $(this).attr("data-pageid");					// show/hide the pages
			$("div [id^='pg_']").addClass("ui-helper-hidden");			// show/hide the pages
			$("#pg_" + pageId).removeClass("ui-helper-hidden");			// show/hide the pages
		});
	
	$("#undoPageSort")
		.button({icons:{primary:'ui-icon-arrowreturnthick-1-w'}})		// init button style
		.click(function(e) {
			$.swUndo({update_key:$(this).data('updateKey')},function(response){
				var sortIdArray = response.split(',');
				for (var i=0; i < sortIdArray.length; i++)
				{
					$('#sortable li[data-pageid='+ sortIdArray[i] +']').appendTo($("#sortable"));
				}
				$("#undoPageSort").fadeOut('fast');
			});
		});	
	
});

function swPage_getById(id)
{
	return $('#pg_' + id);
}

function swPage_setNoUpdates(id,noUpdates)
{
	var page = swPage_getById(id);

	$("#pgID_" + id + " .changeIndicator").remove();
	
	if (noUpdates > 0)
		$("#pgID_" + id).append('<span class="changeIndicator ui-state-error">' + noUpdates + '</span>');
	
	page.data('noUpdates',noUpdates);
}

function swPage_adjustNoUpdates(id,adjustment)
{
	var page = swPage_getById(id);
	var noUpdates = parseInt(page.data('noUpdates')) + adjustment;
	
	swPage_setNoUpdates(id,noUpdates);
}

function swPage_updatePageOrder(sortableMenu,movedPage)
{
	var pagesInOrderById = sortableMenu.sortable("serialize",{key:'pageid',attribute:'data-pageid',expression:'(.*)'});
	
	var data = {
		update_object:'swPage',		// update session parameters
		update_type:'page_sort',
		pages_in_order_by_id:pagesInOrderById
	};
	
	$.swUpdateSession(data,function(response){
		if (response.updateKey == '')
			$("#undoPageSort").fadeOut('fast');			// disable undo button
		else
			$("#undoPageSort").fadeIn('fast')			// enable undo button
				.data('updateKey',response.updateKey);	// add the update key so this can be undone later
	});
}

// generic function to make simple updates
function swPage_setProperty( updateType, inputBox, undoButton, callback )
{
	var data = {
		update_object:'swPage',
		update_object_id:inputBox.data('pageid'),
		update_type:updateType,
		value:inputBox.val()
	};
	
	$.swUpdateSession(data,function(response){
		swPage_setNoUpdates(data.update_object_id,response.noUpdates);
		
		if (response.updateKey == ''){																											// if item was set back to original value
			undoButton.button("option","disabled",true ).data('updateKey','');																			// disable undo button
			inputBox.parent().removeClass('ui-state-error').prev().removeClass('ui-state-error');
		} else {																																// if item was changed
			undoButton.button("option","disabled",false ).data('updateKey',response.updateKey);															// enable undo button
			inputBox.parent().addClass('ui-state-error').prev().addClass('ui-state-error');																// highlight in red
		}
		if (typeof callback == 'function') callback.call(this,response);																		// do callback (if required)
	});
}


// generic function to undo simple updates
function swPage_undoProperty( inputBox, undoButton, callback )
{
	if (undoButton.hasClass('ui-state-disabled')) return;
	var data = {
		update_key:undoButton.data('updateKey'),
		update_object_id:inputBox.data('pageid')
	};
	
	$.swUndo(data,function(response){
		swPage_setNoUpdates(data.update_object_id,response.noUpdates);
		
		undoButton.button("option","disabled",true).data('updateKey','');																		// disable undo button
		inputBox.val(response.undoResponse);																									// put the old value back into the input box
		inputBox.parent().removeClass('ui-state-error').prev().removeClass('ui-state-error');													// unhighlight in red

		if (typeof callback == 'function') callback.call(this,response);																		// do callback (if required)
	});
}

function swPage_updateTitle(inputBox,undoButton)
{
	swPage_setProperty('set_title',inputBox,undoButton,function(response){
		$('#set_page_title' + inputBox.data('pageid')).text(inputBox.val());		// set the title on the sortable button
	});
}
function swPage_updateLinkName(inputBox,undoButton)
{
	swPage_setProperty('set_linkname',inputBox,undoButton,function(response){
		$('#sortable li[data-pageid='+ inputBox.data('pageid') +']').contents().each(function(){
			if (this.nodeType == Node.TEXT_NODE) {
                this.nodeValue = inputBox.val();
            }
		});
	});
}
function swPage_undoTitle(inputBox,undoButton)
{
	swPage_undoProperty( inputBox, undoButton,function(response){
		$('#set_page_title' + inputBox.data('pageid')).text(response.undoResponse);	// put the old value back into the page title
	});
}
function swPage_undoLinkName(inputBox,undoButton)
{
	swPage_undoProperty( inputBox, undoButton,function(response){
		$('#sortable li[data-pageid='+ inputBox.data('pageid') +']').contents().each(function(){
			if (this.nodeType == Node.TEXT_NODE) {
                this.nodeValue = response.undoResponse;								// put the old value back into the sortable button
            }
		});
	});
}


function swWebLog_getById(id)
{
	return $('#wle' + id);
}
function swWebLog_refreshById(id)
{
	$.swRefreshControl({type:'swWebLogEntry',id:id}, function(response){
		swWebLog_init(id,response);
	});
}
function swWebLog_init(id,html)
{
	if (typeof html != 'undefined') swWebLog_getById(id).replaceWith(html);
	common_initButtons(swWebLog_getById(id));
}
function formIsValid(formData)
{
	var isValid;
	var errorText = '';
	
	if (formData.author == '') errorText = '- Please enter a name for the Author';
	if (formData.text == '') errorText += '\n- Please enter the text for this entry';
	
	if (errorText != '') {
		alert('The following errors were found:\n\n' + errorText);
		isValid = false;
	} else {
		isValid = true;
	}
	
	return isValid;
}
function swLogEntry_create(webLogID,formContainer,theForm,webLogEntryContainer,addNewButton)
{
	var data = $.extend(
	{
		update_object:'swWebLog',
		update_object_id:webLogID,
		update_type:'weblog_create'
	},
	theForm.swSerialize());
	
	if (!formIsValid(data)) return; // exit the function
	
	$.swUpdateSession(data,function(response){
		var id = response.responseHTML;

		$.swRefreshControl({type:'swWebLogEntry',id:id}, function(response){
			$(response).hide().prependTo(webLogEntryContainer).slideDown();
			swWebLog_init(id);

			formContainer.slideUp();
			addNewButton.slideDown();
			theForm[0].reset();			// reset the form
		});
	});
}

function swWebLog_update(webLogID,wlEntryID,theForm)
{
	var data = $.extend(
	{
		update_object:'swWebLog',
		update_object_id:webLogID,
		update_type:'weblog_update',
		wlentry_id:wlEntryID
	},
	theForm.swSerialize());

	if (!formIsValid(data)) return; // exit the function
	
	$.swUpdateSession(data,function(response){
		swWebLog_refreshById(wlEntryID);
	});
}
function swWebLog_delete(wlEntryID)
{
	if (!confirm('Are you sure you want to delete?')) return; // exit the function
	
	var ctrl = swWebLog_getById(wlEntryID);
	
	var data = {
		update_object:'swWebLog',
		update_object_id:ctrl.data('weblogid'),
		update_type:'weblog_delete',
		wlentry_id:wlEntryID
	};

	$.swUpdateSession(data,function(){
		ctrl.slideUp('fast',ctrl.remove);
	});
}
function swWebLog_undo(wlEntryID,undoButton)
{
	if (undoButton.hasClass('ui-state-disabled')) return;
	
	var data = {
		update_key:undoButton.data('updateKey'),
		update_object_id:wlEntryID
	};
	
	$.swUndo(data,function(response){
		swWebLog_refreshById(wlEntryID);
	});
}
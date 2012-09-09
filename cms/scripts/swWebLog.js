
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
	
	formContainer.slideUp();
	addNewButton.slideDown();
	
	$.swUpdateSession(data,function(response){
		var id = $(response.responseHTML).data('id');
		webLogEntryContainer.append(response.responseHTML);
		swWebLog_init(id);
		theForm[0].reset();			// reset the form
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
		swWebLog_init(wlEntryID,response.responseHTML);
	});
}
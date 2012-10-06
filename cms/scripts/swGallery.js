// init Galleries
$(document).ready(function(e) {
	swGallery_init();
	
	$(".undoImageSort")
		.button({icons:{primary:'ui-icon-arrowreturnthick-1-w'}})		// init button style
		.click(function(e) {
			var button = $(this);
			$.swUndo({update_key:$(this).data('updateKey')},function(response){
				var sortableImages = button.siblings('.swGalleryImageList');
				var sortIdArray = response.undoResponse.split(',');
				
				for (var i=0; i < sortIdArray.length; i++) {
					swImage_getById(sortIdArray[i]).appendTo(sortableImages);
				}
				
				button.fadeOut('fast');
			});
		});	
});

function swGallery_getById(id)
{
	return $('#g_' + id);
}

// initialize an image after it's been loaded via ajax
function swGallery_init(id)
{
	var context;	// either init ALL galleries (if id is undefined) OR a specific gallery
	
	if (typeof id == 'undefined') {
		context = $(document);
	} else {
		context = swGallery_getById(id);
		common_configureDropDownButtons(context);
		common_initButtons(context);
		common_initRTE(context);
	}
	
	// make images sortable...// sortable gallery images
	$(".swGalleryImageList", context).sortable({placeholder:"ui-state-highlight", update:function() { 
		swGallery_sortImages($(this).data('galleryid'),$(this));
	}});
}

// refresh by id (via ajax or the given html)
/*function swGallery_refreshById(id)
{
	$.swRefreshControl({type:'swGallery',id:id}, function(response){
		swGallery_init(id,response);
	});
}*/

function swGallery_editDescriptionLong(galleryID)
{
	var rte = RTEditors['txtGalleryDescLongHTML' + galleryID];		// get the RTE object
	
	var data = {
		update_object:'swGallery',
		update_object_id:galleryID,
		update_type:'gallery_update_desc_long',
		value:rte.get_content()			// get the html from the RTE object
	};
	
	$.swUpdateSession(data,function(response){
		$('#divGalleryDescLongEdit' + galleryID).slideUp();
		$('#divGalleryDescLong' + galleryID).html(data.value).slideDown();
	});
}

function swGallery_sortImages(galleryID,images)
{
	var imagesInOrderById = images.sortable("serialize",{key:'imageid',attribute:'data-id',expression:'(.*)'});
	
	var data = {
		update_object:'swGallery',		// update session parameters
		update_object_id:galleryID,		// update session parameters
		update_type:'sort_images',
		images_in_order_by_id:imagesInOrderById
	};
	
	$.swUpdateSession(data,function(response){
		if (response.updateKey == '')
			images.siblings(".undoImageSort").fadeOut('fast');
		else
			images.siblings(".undoImageSort").fadeIn('fast').data('updateKey',response.updateKey);
	});
}

function swGallery_deleteImage(galleryID,imageID)
{
	if (confirm('Are you sure you want to delete?')) {
		_swImage_setLoadingMessage(imageID,'Deleting...');
		
		var data = {
			update_object:"swGallery",
			update_object_id:galleryID,
			update_type:"delete_image",
			img_id:imageID
		};
		
		$.swUpdateSession(data,function(){
			swImage_getById(imageID).remove();
		});
	}
}
function swImage_getById(id)
{
	return $('#img_' + id);
}

// initialize an image after it's been loaded via ajax
function swImage_init(id,html)
{
	swImage_getById(id).replaceWith(html);
	common_configureDropDownButtons(swImage_getById(id));
}

// refresh by id (via ajax or the given html)
function swImage_refreshById(id)
{
	_swImage_setLoadingMessage();
	
	$.swRefreshControl({type:'swImage',id:id}, function(response){
		swImage_init(id,response);
	});
}

// refresh all featured images under the given context
function swImage_refreshFeaturedImages(context,except_id)
{
	$('.featured',context).not(swImage_getById(except_id)).each(function(){
		swImage_refreshById($(this).attr('data-id'));
	});
}

// creat new image html (for the ajax data to load into)
function swImage_createNew(id,container)
{
	container.append('<li id="img_' + id + '" class="swImage ui-state-error"></li>');
	_swImage_setLoadingMessage(id);
	return swImage_getById(id);
}
function _swImage_setLoadingMessage(id,message) {
	message = (typeof message == 'undefined') ? 'Loading...' : message;
	swImage_getById(id).html('<br /><br /><br /><img src="images/ajax-loader2.gif" /><p>' + message + '</p>');
}

// exit ALL image croppers currently in use
function swImage_exitImageCropper()
{
	$('.imgAreaSelect').each(function(){
		$(this).imgAreaSelect({remove: true})
	});
}

// start uploading images to a gallery
function _addImageFromLocal(uid,galleryID)
{
	var uploadBox = $('#div_uploadbox' + uid);
	var btnAddImage = $('#btnAddMore' + uid);
	var status = $('#div_status' + uid);
	var cropArea = $('#div_crop_area' + uid).html('');
	
	$('#div_gallery' + uid).slideUp();
	uploadBox.slideDown();
	
	new AjaxUpload(btnAddImage, {
					action: 'controls/image_uploader.php',
					name: 'uploadfile[]',
					data: {gallery_id:galleryID},
					multiple: false,
					onSubmit: function(file, ext){
						 if (! (ext && /^(jpg|png|jpeg|gif)$/i.test(ext))){
							// extension is not allowed
							status.text('Only JPG, PNG or GIF files are allowed');
							return false;
						}
						$('#div_loading' + uid).slideDown();
						$('.div_buttons',uploadBox).slideUp();
						status.text('');
					},
					onComplete: function(file, response){
						$('#div_loading' + uid).hide();
						cropArea.append(response);
						
						var uploader_uid = $('input[name=uid]',response).attr('value');
						var PathToImage = $('#img_preview' + uploader_uid,response).attr('src');
						
						$('#name_' + uploader_uid).attr('value',file.replace(/(.*)\..+$/, '$1')).focus();
						
						swImage_initCrop(uploader_uid,PathToImage,170,100);	// start the cropping process
						
						$('.div_buttons',uploadBox).slideDown();
					}
	});
}

// stop upload images to a gallery
function swImage_uploadCancel(uid)
{
	swImage_exitImageCropper();
	$('#div_uploadbox' + uid).slideUp();
	$('#div_edit_box' + uid).slideUp();
	$('#div_initializing' + uid).hide();
	$('#upload_form' + uid).hide();
	$('#div_gallery' + uid).slideDown();
}

// show loading message while uploading to a gallery
function swImage_showLoadingBox(uid)
{
	swImage_uploadCancel(uid);
	$('#div_gallery' + uid).slideUp();
	$('#div_loading' + uid).slideDown();
}

// this function saves all of the croped images in the given context
function swImage_SaveToSession(context,uid)
{
	swImage_uploadCancel(uid);
	
	$('form.gallery_image_upload',$('#' + context)).each(function(){
		var theForm = $(this);
		var formData = theForm.swSerialize();
		
		var imgID = $('input[name=img_id]',theForm).attr('value');
		var imgElement = swImage_getById(imgID);
		
		// it the image doesn't exist yet then create one
		// and add it to the end of the gallery
		if (imgElement.length == 0)
			swImage_createNew(imgID,$('.swGalleryImageList',$('#div_gallery' + uid)));
		else
			_swImage_setLoadingMessage(imgID);
		
		$.swUpdateSession(formData,function(response){
			swImage_init(imgID,response.responseHTML);
		})
	});
}

// once an image has been uploaded this function kicks off the cropping process
function swImage_initCrop(uid,PathToImage,cropWidth,cropHeight)
{
	_setDimentionsOfCropPreview(uid,cropWidth,cropHeight)
	_preloadImageThenCrop(uid,PathToImage,cropWidth,cropHeight);
}

// this function will crop an existing image
function swImage_initReCrop(imgID,imgName,imgSize,galleryID,uid,cropWidth,cropHeight,cropFeatured,disableCancelButton)
{
	swImage_showLoadingBox(uid);
	
	var cropFeatured = (typeof(cropFeatured) == 'undefined') ? false : cropFeatured;
	var disableCancelButton = (typeof(disableCancelButton) == 'undefined') ? false : disableCancelButton;
	
	if (disableCancelButton) $('#cancel_edit_image' + uid).hide();
	else $('#cancel_edit_image' + uid).show();
	
	var divEditBox = $('#div_edit_box' + uid);
	var buttonControls = $('#buttonControls',divEditBox).hide();
	
	$.swAjax(
		'controls/image_editor.php',
		{
			gallery_id:galleryID,
			image_id:imgID,
			crop_featured:cropFeatured
		},
		function(response){
			$('#div_loading' + uid).hide();
			$('#div_edit_crop_area' + uid).empty().append(response);
			
			var uploader_uid = $('input[name=uid]',response).attr('value');
			var PathToImage = $('input[name=image_src]',response).attr('value');
			
			$('#name_' + uploader_uid).attr('value',imgName);				// set the name of the image (to be renamed)
			$('#img_id_' + uploader_uid).attr('value',imgID);				// set the id of the image to be sent to the server
			$('#img_size_' + uploader_uid).attr('value',imgSize);			// set the size of the image to be sent to the server
			
			_setDimentionsOfCropPreview(uploader_uid,cropWidth,cropHeight)
			_preloadImageThenCrop(uploader_uid,PathToImage,cropWidth,cropHeight,imgSize);		// preload the image
			
			divEditBox.show();
			buttonControls.slideDown();
		})
}

/*
	All functions below are specific to image cropping
*/
function _setDimentionsOfCropPreview(uid,cropWidth,cropHeight)
{
	$('#div_thumb' + uid).css('width',cropWidth).css('height',cropHeight); // set the dimentions of the crop preview
}
function _preloadImageThenCrop(uid,pathToImage,cropWidth,cropHeight)
{
	var img = $('#img_preview' + uid);
	var thumb = $('#img_thumb' + uid);
	
	var preloadImage = new Image();
	
	preloadImage.onload = function() {	// when the image has loaded...
		$('#div_initializing' + uid).hide();	// hide the loading div
		$('#div_loading' + uid).hide();		// hide the loading div
		$('#div_edit_box' + uid).show();	// hide the loading div
		$('#upload_form' + uid).show();			// show the crop div
		
		var originalWidth = this.width;
		var originalHeight = this.height;
		
		_resizePreview(img,originalWidth,originalHeight);			// now the image has loaded, resize to fit on document
		_startCrop(uid,cropWidth,cropHeight,originalWidth,originalHeight); // initialize the crop feature now that the image has been resized
	}
	
	img.removeAttr('width').removeAttr('height')	// remove any previous dimentions
	   .attr('src', pathToImage);				// set the preview image first
	thumb.attr('src', pathToImage);				// set the thumb image next
	preloadImage.src = pathToImage;				// now we can start the pre-load
}

function _resizePreview(img,originalWidth,originalHeight)		// resize the preview so it can fit on the page
{
	var maxWidth = 270;
	var maxHeight = 350;
	var newWidth = originalWidth;
	var newHeight = originalHeight;
	
	if (newWidth > maxWidth) {
		newHeight = newHeight * (maxWidth / newWidth);
		newWidth = maxWidth;
	}
	
	if (newHeight > maxHeight) {
		newWidth = newWidth * (maxHeight / newHeight);
		newHeight = maxHeight;
	}
	
	img.attr('width',newWidth).attr('height',newHeight);
}

function _startCrop(uid,cropWidth,cropHeight,originalWidth,originalHeight)	// initialize the crop feature
{
	var img = $('#img_preview' + uid);
	
	var resizedWidth = img.width();		// the width of the scaled down image
	var resizedHeight = img.height();	// the hegiht of the scaled down image
	
	if (resizedWidth == null || resizedWidth == 'undefined' || resizedWidth == 0) {
		$('#upload_form' + uid).remove();
		alert('Error loading image');
		return
	} // stop if we can load the image
	
	var initCropWidth = cropWidth;		// calculate the initial selected area size
	var initCropHeight = cropHeight;		// calculate the initial selected area size
	
	while (initCropWidth > resizedWidth || initCropHeight > resizedHeight) {
		initCropWidth *= .75;		// calculate the initial selected area size
		initCropHeight *= .75;		// calculate the initial selected area size
	}
	
	var x1 = (resizedWidth / 2) - (initCropWidth / 2);		// set the initial selected area position
	var y1 = (resizedHeight / 2) - (initCropHeight / 2);	// set the initial selected area position
	var x2 = x1 + initCropWidth;							// set the initial selected area position
	var y2 = y1 + initCropHeight;							// set the initial selected area position
	
	img.imgAreaSelect({remove: true})		// remove any previous AreaSelect plugins
	img.imgAreaSelect({aspectRatio: _reduceRatio(cropWidth,cropHeight),	// fix the ratio (based on the crop width/height)
					   handles: true,									// puts square handles on the corners
					   x1: x1, y1: y1, x2: x2, y2: y2,					// sets the initial selected area
					   fadeSpeed: 200,									// fade in the initial selection
					   persistent: false,								// allow user to de-select/re-select
					   onInit: _adjustThumbnail,
					   onSelectChange: _adjustThumbnail});
	
	function _adjustThumbnail(img,selection)	// this function runs everytime the cropped selection has changed
	{
		var scaleX = cropWidth / (selection.width || 1);		// find the difference between the selected
		var scaleY = cropHeight / (selection.height || 1);		// area and the thumbnail size
		
		$('#img_thumb' + uid).css({						// resize the thumbnail image
			width: Math.round(scaleX * resizedWidth) + 'px',
			height: Math.round(scaleY * resizedHeight) + 'px',
			marginLeft: '-' + Math.round(scaleX * selection.x1) + 'px',
			marginTop: '-' + Math.round(scaleY * selection.y1) + 'px'
		});
		
		// now we've sorted the thumbnail we need to workout the 
		// selected area relative to the size of the actual image
		// to send to the php script
		scaleX = originalWidth / resizedWidth;					// find the diference between the original resized image
		scaleY = originalHeight / resizedHeight;				// and the real size of the image
		
		$('#x_' + uid).attr('value',scaleX * selection.x1);		// set crop area for php script to use later
		$('#y_' + uid).attr('value',scaleY * selection.y1);		// set crop area for php script to use later
		$('#w_' + uid).attr('value',scaleX * selection.width);	// set crop area for php script to use later
		$('#h_' + uid).attr('value',scaleY * selection.height);	// set crop area for php script to use later
		$('#tw_' + uid).attr('value',cropWidth);				// set crop area for php script to use later
		$('#th_' + uid).attr('value',cropHeight);				// set crop area for php script to use later
	}
}
function _reduceRatio(numerator, denominator) {
	var gcd, temp, divisor;
	
	// from: http://pages.pacificcoast.net/~cazelais/euclid.html
	gcd = function (a, b) { 
		if (b === 0) return a;
		return gcd(b, a % b);
	}
	
	// take care of some simple cases
	if (!isInt(numerator) || !isInt(denominator)) return '? : ?';
	if (numerator === denominator) return '1 : 1';
	
	// make sure numerator is always the larger number
	if (+numerator < +denominator) {
		temp        = numerator;
		numerator   = denominator;
		denominator = temp;
	}
	
	divisor = gcd(+numerator, +denominator);
	
	return 'undefined' === typeof temp ? (numerator / divisor) + ' : ' + (denominator / divisor) : (denominator / divisor) + ' : ' + (numerator / divisor);
}
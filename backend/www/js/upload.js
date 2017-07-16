 Init = function (paramsObj) {
var addImageButton = $('#addImageButton');
var itemsListCnt = $('#imageListContainer');
var params = {
maxFileSize: '8000000',
imageIdName: 'ImageIds',
maxImageCountText: $('#maxImageCountText'),
currentImageCountText: $('#currentImageCountText'),
insertBeforeElement: addImageButton,
fileInput: $('#fileInput'),
imageListContainer: itemsListCnt,
url: '/imageupload/LoadMultiFile/',
dropZone: $('#dropZone'),
dropNotify: $('#dropNotify'),
onImageChangeHandler: function (count, maxCount) {
if (count == maxCount) {
$('#buyPro').show()
addImageButton.hide();
} else {
$('#buyPro').hide()
addImageButton.show();
}
},
files:
[
]
};
$.extend(params, paramsObj);
(new Utils.LotImageUploader()).Init(params);
};
